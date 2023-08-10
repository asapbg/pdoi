<?php

namespace App\Services;

use App\Enums\ApplicationEventsEnum;
use App\Enums\MailTemplateTypesEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Event;
use App\Models\File;
use App\Models\MailTemplates;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationEvent;
use App\Notifications\NotifySubjectNewApplication;
use App\Notifications\NotifyUserExtendTerm;
use App\Notifications\NotifyUserForAppForward;
use App\Notifications\NotifyUserForAppStatus;
use App\Notifications\NotifyUserNeedMoreInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class ApplicationService
{
    private PdoiApplication $application;
    private int $userId;

    public function __construct(PdoiApplication $application)
    {
        $this->application = $application;
        $this->userId = auth()->user() ? auth()->user()->id : null;
    }

    public function registerEvent(int $eventType, $data = [], $disableCommunication = false): ?PdoiApplicationEvent
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
                    if(
                        !in_array($eventConfig->id, $allowedNextEvents)
                        && !($this->application->currentEvent->event_type == ApplicationEventsEnum::FINAL_DECISION->value
                            && $eventConfig->app_event == ApplicationEventsEnum::RENEW_PROCEDURE->value
                            && PdoiApplicationStatusesEnum::canRenew((int)$this->application->status))
                        && !(in_array($eventConfig->app_event, ApplicationEventsEnum::forwardGroupEvents())
                            && PdoiApplicationStatusesEnum::canForward($this->application->status))
                    ) {
                        throw new \Exception('Not allowed next event to current one: '.$this->application->currentEvent->event->name);
                    }
                }

                $newEvent = new PdoiApplicationEvent();
                $newEvent->event_type = $eventConfig->app_event;
                $newEvent->event_date = Carbon::now();
                if ($eventConfig->days) {
                    //event waiting time
                    $eventEndDate = match ((int)$eventConfig->app_event){
                        ApplicationEventsEnum::ASK_FOR_INFO->value => Carbon::now()->addDays($eventConfig->days),
                        default => null
                    };
                    //extending response time for application
                    $extendTermDate = match ((int)$eventConfig->app_event){
                        ApplicationEventsEnum::EXTEND_TERM->value => Carbon::parse($this->application->response_end_time)->addDays($eventConfig->days),
                        default => null
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
                if ($eventConfig->extend_terms_reason_id) {
                    $newEvent->event_reason = $eventConfig->extend_terms_reason_id;
                }
                if ($eventConfig->old_resp_subject && isset($data['old_subject']) && (int)$data['old_subject']) {
                    $newEvent->old_resp_subject_id = (int)$data['old_subject'];
                }
                if ($eventConfig->new_resp_subject && isset($data['new_resp_subject_id']) && (int)$data['new_resp_subject_id']) {
                    $newEvent->new_resp_subject_id = (int)$data['new_resp_subject_id'];
                }
                if ($eventConfig->new_resp_subject && isset($data['new_resp_subject_eik']) && isset($data['new_resp_subject_name'])) {
                    $newEvent->subject_eik = $data['new_resp_subject_eik'];
                    $newEvent->subject_name = $data['new_resp_subject_name'];
                }
                if ($eventConfig->court_decision && isset($data['decision']) && (int)$data['decision']) {
                    $newEvent->court_decision = (int)$data['decision'];
                }
                //add decision to final event and reason refusal if this is the case
                if ($eventConfig->app_event == ApplicationEventsEnum::FINAL_DECISION->value) {
                    $newEvent->event_reason = (int)$data['final_status'] ?? null;

                    if( (int)$data['final_status'] == PdoiApplicationStatusesEnum::NOT_APPROVED->value
                        && isset($data['refuse_reason']) ) {
                        $newEvent->reason_not_approved = (int)$data['refuse_reason'];
                    }
                }

                $this->application->save();
                $newEvent->user_reg = !in_array($eventConfig->app_event, [ApplicationEventsEnum::SEND_TO_SEOS->value, ApplicationEventsEnum::APPROVE_BY_SEOS->value]) ? $this->userId : null;
                $newEvent->status = $eventConfig->event_status;
                $this->application->events()->save($newEvent);
                $newEvent->refresh();

                //Save user attached files
                if( isset($data['files']) && sizeof($data['files']) ) {
                    $this->attachEventFiles($newEvent, $data['files'], $data['file_description'], $data['file_visible'] ?? []);
                }
                if ($eventConfig->app_event == ApplicationEventsEnum::RENEW_PROCEDURE->value) {
                    if( isset($data['file_decision']) && !is_null($data['file_decision']) ) {
                        $this->attachEventFiles($newEvent, [$data['file_decision']], [], []);
                    }
                }

                //Set communication and status
                $this->setApplicationStatus($eventConfig, $data);
                if(!$disableCommunication) {
                    $this->scheduleCommunication($eventConfig);
                }

                if( in_array($eventConfig->app_event, ApplicationEventsEnum::forwardGroupEvents()) ) {
                    switch ($eventConfig->app_event) {
                        case ApplicationEventsEnum::FORWARD->value:
                        case ApplicationEventsEnum::FORWARD_TO_SUB_SUBJECT->value:
                            //generate new application for new subject
                            self::generateNewApplication($data, $this->application->id);
                            break;
                        case ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUBJECT->value:
                        case ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT->value:
                            //generate new application for new subject
                            self::generateNewApplication($data, $this->application->id, 0, true);
                            break;
                        default:
                            break;
                    }
                    //generate new application for current subject
                    if( isset($data['current_subject_user_request']) && !empty($data['current_subject_user_request']) ) {
                        self::generateNewApplication($data, $this->application->id, $this->application->response_subject_id);
                    }
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            logError('Register event ('. $eventConfig->name .') for application ID '.$this->application->id, $e->getMessage());
        }

        return $newEvent;
    }

    /**
     * @param array $data
     * @param int $mainAppId
     * @param int $subjectId //current subject id when we generate new application for current subject
     * @return PdoiApplication
     * @throws \Exception
     */
    private function generateNewApplication(array $data, int $mainAppId, int $subjectId = 0, bool $notRegisteredSubject = false): PdoiApplication
    {
        $newApplicationData = [
            'user_reg' => $this->application->user_reg,
            'request' => htmlentities(stripHtmlTags($data['subject_user_request'])),
            'status' => PdoiApplicationStatusesEnum::RECEIVED->value,
            'application_uri' => round(microtime(true)).'-'. displayDate(Carbon::now()),
            'parent_id' => $mainAppId
//            'user_attached_files' => isset($data['files']) ? sizeof($data['files']) : 0,
        ];

        if( $notRegisteredSubject ) {
            //save new subject eik and name and leave response_subject_id null when subject is not registered in platform
            $newApplicationData['not_registered_subject_eik'] = $data['new_resp_subject_eik'] ?? null;
            $newApplicationData['not_registered_subject_name'] = $data['new_resp_subject_name'] ?? null;
        } else{
            $newApplicationData['response_subject_id'] = $subjectId ? $subjectId : $data['new_resp_subject_id'];
        }

        $newAppRequest = $subjectId ? $data['current_subject_user_request'] : $data['subject_user_request'];
        $newApplicationData['request'] = htmlentities(stripHtmlTags($newAppRequest));

        //date for the new application with same subject should stay the same
        if($subjectId) {
            $newApplicationData['response_end_time'] = $this->application->response_end_time;
        }

        $copyApplicationFields = ['applicant_type', 'applicant_identity', 'email', 'post_code'
            , 'full_names', 'country_id', 'area_id', 'municipality_id', 'settlement_id', 'address'
            , 'address_second', 'phone', 'email_publication', 'names_publication', 'address_publication'
            , 'phone_publication', 'profile_type'];
        foreach ($copyApplicationFields as $field) {
            $newApplicationData[$field] = $this->application->{$field};
        }
        $newApplication = new PdoiApplication($newApplicationData);
        $appService = new ApplicationService($newApplication);

        //Register event: register first app event
        $receivedEvent = $appService->registerEvent(ApplicationEventsEnum::SEND->value);
        if ( is_null($receivedEvent) ) {
            throw new \Exception('Apply application (front). Operation roll back because cant\'t register '.ApplicationEventsEnum::SEND->name. ' event');
        }

        //Attach user files from original application
        if( $this->application->userFiles->count() ) {
            foreach ($this->application->userFiles as $f) {
                $newFile = $f->replicate();
                $newFile->id_object = $newApplication->id;
                $newFile->save();
            }
        }

        //TODO communication for not existing subject
        //communication to subject if existing in platform
        if( isset($data['new_resp_subject_id']) ) {
            $subject = $newApplication->responseSubject;
            //Communication: notify subject for new application
            $instructionData = array(
                'to_name' => $data['to_name'] ?? '',
                'reg_number' => $newApplication->parent->application_uri,
                'date_apply' => displayDate($newApplication->created_at),
                'administration' => $newApplication->parent->responseSubject->subject_name,
                'applicant' => $newApplication->parent->full_names,
                //
                'new_reg_number' => $newApplication->application_uri,
                'forward_administration' => $newApplication->responseSubject->subject_name,
                'forward_date_apply' => displayDate(Carbon::now()),
            );
            $instructionTemplate = MailTemplates::where('type', '=', MailTemplateTypesEnum::RZS_MANUAL_FORWARD->value)->first();
            $message = $instructionTemplate ? Lang::get($instructionTemplate->content, $instructionData) : '';
            $notifyData['message'] = htmlentities($message);
            if( isset($data['add_text']) && !empty($data['add_text']) ) {
                $notifyData['comment'] = htmlentities(stripHtmlTags($data['add_text']));
            }

//            //TODO fix me simulation remove after communication is ready. For now we simulate approve by RKS (деловодна система)
//            $lastNotify = DB::table('notifications')
//                ->where('type', 'App\Notifications\NotifySubjectNewApplication')
//                ->latest()->limit(1)->get()->pluck('id');
//            if(isset($lastNotify[0])) {
//                $appService = new ApplicationService($newApplication);
//                $appService->communicationCallback($lastNotify[0]);
//            }
        }

        $appService->generatePdf($newApplication);
        $newApplication->refresh();
        $subject->notify(new NotifySubjectNewApplication($newApplication, $notifyData));
        sleep(1);

        return $newApplication;
    }

    public static function generatePdf($application)
    {
        $fileName = 'zayavlenie_ZDOI_'.displayDate($application->created_at).'.pdf';
        $pdfFile = Pdf::loadView('pdf.application_doc', ['application' => $application]);
        Storage::disk('local')->put($application->fileFolder.$fileName, "\xEF\xBB\xBF" . $pdfFile->output());
        $newFile = new File([
            'code_object' => File::CODE_OBJ_APPLICATION,
            'filename' => $fileName,
            'content_type' => 'application/pdf',
            'path' => $application->fileFolder.$fileName,
        ]);
        $application->files()->save($newFile);
    }

    private function scheduleCommunication($event)
    {
        match ($event->app_event){
            ApplicationEventsEnum::SEND->value,
            ApplicationEventsEnum::SEND_TO_SEOS->value,
            ApplicationEventsEnum::APPROVE_BY_SEOS->value,
            ApplicationEventsEnum::FINAL_DECISION->value => $this->application->applicant->notify(new NotifyUserForAppStatus($this->application)),
            ApplicationEventsEnum::ASK_FOR_INFO->value => $this->application->applicant->notify(new NotifyUserNeedMoreInfo($this->application)),
            ApplicationEventsEnum::EXTEND_TERM->value => $this->application->applicant->notify(new NotifyUserExtendTerm($this->application)),
            ApplicationEventsEnum::FORWARD->value => $this->application->applicant->notify(new NotifyUserForAppForward($this->application)),
            default => null
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
            if( $event->app_event != ApplicationEventsEnum::RENEW_PROCEDURE->value || (isset($data['reopen']) && (int)$data['reopen']) ) {
                $this->application->status = $event->app_status;
                $this->application->status_date = Carbon::now();
            }

            //when registering application after forward to same object do not set new deadline
            if( $event->app_event == ApplicationEventsEnum::SEND->value &&
                (!$this->application->parent_id || $this->application->response_subject_id != $this->application->parent->response_subject_id ) ) {
                //потвърдено от деловодна система
                $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_APPLY);
            } elseif ( $event->app_event == ApplicationEventsEnum::APPROVE_BY_SEOS->value) {
                //потвърдено от деловодна система
                $this->application->registration_date = Carbon::now();
                if(!$this->application->parent_id || $this->application->response_subject_id != $this->application->parent->response_subject_id ) {
                    $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION);
                }
            } elseif( $event->app_event == ApplicationEventsEnum::GIVE_INFO->value ) {
                //Предоставяне на допълнителна информация
                //крайния срок се удължава като се изчислява 14 дни от датата на уточняването на предмета на исканата обществена информация
                $this->application->response_end_time = Carbon::now()->addDays(PdoiApplication::DAYS_AFTER_GIVE_INFORMATION);
            }
        }
        $this->application->save();
    }

    private function attachEventFiles($event, $files, $descriptions, $visiblelity)
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
                'description' => $descriptions[$key] ?? null,
                'user_reg' => $this->userId,
                'visible_on_site' => $visiblelity[$key] ?? 0,
            ]);
            $event->files()->save($newFile);
            $ocr = new FileOcr($newFile->refresh());
            $ocr->extractText();
        }
    }

    public function communicationCallback($notification)
    {
        if( !$notification ) {
            logError('Communication callback', 'Missing notification object');
            exit;
        }

        $notifiable = $notification->notifiable_type::find($notification->notifiable_id);

        if( !$notifiable ) {
            logError('Communication callback', 'Notifiable not found: '.$notification->notifiable_id.'('.$notification->notifiable_type.')');
            exit;
        }
        //process depending on notification type
        if( $notification->type == 'App\Notifications\NotifySubjectNewApplication' ){
            switch ($notification->type_channel)
            {
                case PdoiSubjectDeliveryMethodsEnum::SEOS->value://деловодна система
                    $this->registerEvent(ApplicationEventsEnum::SEND_TO_SEOS->value);//изпратено към деловодна система
                    //TODO fix me simulation
                    $this->registerEvent(ApplicationEventsEnum::APPROVE_BY_SEOS->value);//потвърдено от деловодна система
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
