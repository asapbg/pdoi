<?php

namespace App\Services;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Event;
use App\Models\File;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationEvent;
use App\Notifications\NotifyUserExtendTerm;
use App\Notifications\NotifyUserForAppStatus;
use App\Notifications\NotifyUserNeedMoreInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    private PdoiApplication $application;
    private int $userId;

    public function __construct(PdoiApplication $application)
    {
        $this->application = $application;
        $this->userId = auth()->user() ? auth()->user()->id : null;
    }

    public function registerEvent(int $eventType, $data = []): ?PdoiApplicationEvent
    {
        $newEvent = null;
        DB::beginTransaction();
        try {
            $eventConfig = Event::where('app_event', $eventType)
                ->when((isset($data['event']) && $data['event']), function ($q) use($data) {
                    return $q->where('id', '=', (int)$data['event']);
                })->first();

            if( $eventConfig ) {
                if( $this->application->currentEvent ) {
                    $allowedNextEvents = $this->application->currentEvent->event->nextEvents->pluck('id')->toArray();
                    if( !in_array($eventConfig->id, $allowedNextEvents) ) {
                        throw new \Exception('Not allowed next event to current one: '.$this->application->currentEvent->event->name);
                    }
                }

                $newEvent = new PdoiApplicationEvent();
                $newEvent->event_type = $eventConfig->app_event;
                $newEvent->event_date = Carbon::now();
                if ($eventConfig->days) {
                    //event waiting time
                    $eventEndDate = match ($eventConfig->app_event){
                        ApplicationEventsEnum::ASK_FOR_INFO->value => Carbon::now()->addDays($eventConfig->days)
                    };
                    //extending response time for application
                    $extendTermDate = match ($eventConfig->app_event){
                        ApplicationEventsEnum::EXTEND_TERM->value => Carbon::parse($this->application->response_end_time)->addDays($eventConfig->days)
                    };
                    if( $eventEndDate ) {
                        $newEvent->event_end_date = $eventEndDate;
                    }
                    if( $extendTermDate ) {
                        $this->application->response_end_time = $extendTermDate;
                    }
                }
                if ($eventConfig->add_text && isset($data['add_text']) && !empty($data['add_text'])) {
                    $newEvent->add_text = htmlentities(stripHtmlTags($data['add_text']));
                }
                if ($eventConfig->old_resp_subject_id && isset($data['old_subject']) && (int)$data['old_subject']) {
                    $newEvent->old_resp_subject_id = (int)$data['old_subject'];
                }
                if ($eventConfig->new_resp_subject_id && isset($data['new_subject']) && (int)$data['new_subject']) {
                    $newEvent->new_resp_subject_id = (int)$data['new_subject'];
                }

                $this->application->save();
                $newEvent->user_reg = !in_array($eventConfig->app_event, [ApplicationEventsEnum::SEND_TO_RKS->value, ApplicationEventsEnum::APPROVE_BY_RKS->value]) ? $this->userId : null;
                $newEvent->status = $eventConfig->event_status;
                $this->application->events()->save($newEvent);
                $newEvent->refresh();

                //Save user attached files
                if( isset($data['files']) && sizeof($data['files']) ) {
                    $this->attachEventFiles($newEvent, $data['files'], $data['file_description']);
                }

                //Set communication and status
                $this->setApplicationStatus($eventConfig, $data);
                $this->scheduleCommunication($eventConfig);
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            logError('Register event ('. $eventConfig->name .') for application ID '.$this->application->id, $e->getMessage());
        }

        return $newEvent;
    }

    private function scheduleCommunication($event)
    {
        match ($event->app_event){
            ApplicationEventsEnum::SEND->value,
            ApplicationEventsEnum::SEND_TO_RKS->value,
            ApplicationEventsEnum::APPROVE_BY_RKS->value,
            ApplicationEventsEnum::FINAL_DECISION->value => $this->application->applicant->notify(new NotifyUserForAppStatus($this->application)),
            ApplicationEventsEnum::ASK_FOR_INFO->value => $this->application->applicant->notify(new NotifyUserNeedMoreInfo($this->application)),
            ApplicationEventsEnum::EXTEND_TERM->value => $this->application->applicant->notify(new NotifyUserExtendTerm($this->application)),
        };
    }

    private function setApplicationStatus($event, $data)
    {
        if ($event->app_event == ApplicationEventsEnum::FINAL_DECISION->value) {
            if (!isset($data['final_status']) || !PdoiApplicationStatusesEnum::isFinalStatus($data['final_status'])) {
                throw new \Exception('Try to set not final application status: ' . PdoiApplicationStatusesEnum::keyByValue((int)$data['final_status']));
            }
            if (isset($data['add_text']) && !empty($data['add_text'])) {
                $this->application->response = htmlentities(stripHtmlTags($data['add_text']));
            }
            $this->application->status = $data['final_status'];
            $this->application->status_date = Carbon::now();
            $this->application->response_date = Carbon::now();
            $this->application->replay_in_time = Carbon::now()->diffInDays($this->application->registration_date);
        }else {
            $this->application->status = $event->app_status;
            $this->application->status_date = Carbon::now();

            if( $event->app_event == ApplicationEventsEnum::SEND->value) {
                //потвърдено от деловодна система
                $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_APPLY);
            } elseif ( $event->app_event == ApplicationEventsEnum::APPROVE_BY_RKS->value) {
                //потвърдено от деловодна система
                $this->application->registration_date = Carbon::now();
                $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION);
            }elseif( $event->app_event == ApplicationEventsEnum::GIVE_INFO->value ) {
                //Предоставяне на допълнителна информация
                //крайния срок се удължава като се изчислява 14 дни от датата на уточняването на предмета на исканата обществена информация
                $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_GIVE_INFORMATION);
            }
        }
        $this->application->save();
    }

    private function attachEventFiles($event, $files, $descriptions)
    {
        foreach ($files as $key => $file) {
            $fileNameToStore = ($key + 1).'_e_'.round(microtime(true)).'.'.$file->getClientOriginalExtension();
            // Upload File
            $file->storeAs($this->application->fileFolder, $fileNameToStore, 'local');
            $newFile = new File([
                'code_object' => File::CODE_OBJ_EVENT,
                'filename' => $fileNameToStore,
                'content_type' => $file->getClientMimeType(),
                'path' => $this->application->fileFolder.$fileNameToStore,
                'description' => $descriptions[$key],
                'user_reg' => $this->userId,
            ]);
            $event->files()->save($newFile);
            $ocr = new FileOcr($newFile->refresh());
            $ocr->extractText();
        }
    }

    public function communicationCallback($data)
    {
        //data format:
        //1 {error:1, message: 'dsfsfsdfdsf}
        //1 {notification_id:1}
        $arrayData = json_decode($data, true);
        if( !$arrayData ) {
            logError('Communication callback', 'Bad format data');
            exit;
        }
        if( isset($arrayData['error']) ) {
            //do something with error message
            exit;
        }

        if( !isset($arrayData['notification_id']) ) {
            logError('Communication callback', 'Missing notification id: '.$data);
            exit;
        }

        $notification = DB::table('notifications')->find($arrayData['notification_id']);
        if( !$notification ) {
            logError('Communication callback', 'Notification not found: '.$data);
            exit;
        }

        $notifiable = $notification->notifiable_type::find($notification->notifiable_id);

        if( !$notifiable ) {
            logError('Communication callback', 'Notifiable not found: '.$data);
            exit;
        }
        //process depending on notification type
        if( $notification->type == 'App\Notifications\NotifySubjectNewApplication' ){
            switch ($notification->type_channel)
            {
                case PdoiSubjectDeliveryMethodsEnum::RKS->value://деловодна система
                    $this->registerEvent(ApplicationEventsEnum::SEND_TO_RKS->value);//изпратено към деловодна система
                    //TODO fix me simulation
                    $this->registerEvent(ApplicationEventsEnum::APPROVE_BY_RKS->value);//потвърдено от деловодна система
                    break;
                default://email, //система за сигурно връчване
                    $this->application->status = PdoiApplicationStatusesEnum::IN_PROCESS->value;
                    $this->application->status_date = Carbon::now();
                    $this->application->registration_date = Carbon::now();
                    $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION);
                    $this->application->save();
                    $this->application->refresh();
                    $this->application->applicant->notify(new NotifyUserForAppStatus($this->application));
            }
        }
    }
}
