<?php

namespace App\Services;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\Event;
use App\Models\File;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationEvent;
use Carbon\Carbon;

class ApplicationService
{
    private PdoiApplication $application;
    private $user;

    public function __construct(PdoiApplication $application)
    {
        $this->application = $application;
        $this->user = auth()->user() ? auth()->user()->id : 0;
    }

    public function registerEvent(int $eventType, $data = []): ?PdoiApplicationEvent
    {
        $newEvent = null;

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
                    $eventEndDate = match ($eventConfig->app_event){
                        ApplicationEventsEnum::ASK_FOR_INFO->value => Carbon::now()->addDays($eventConfig->days),
                        ApplicationEventsEnum::EXTEND_TERM->value => Carbon::parse($this->application->response_end_time)->addDays($eventConfig->days)
                    };
                    $newEvent->event_end_date = $eventEndDate;

                    if( $eventConfig->event_status == Event::EVENT_STATUS_COMPLETED ) {
                        $this->application->response_end_time = $eventEndDate;
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

                $newEvent->user_reg = $this->user;
                $newEvent->status = $eventConfig->event_status;
                $this->application->events()->save($newEvent);
                $newEvent->refresh();

                //Save user attached files
                if( isset($data['files']) && sizeof($data['files']) ) {
                    $this->attachEventFiles($newEvent, $data['files'], $data['file_description']);
                }

                if( $eventConfig->app_event == ApplicationEventsEnum::FINAL_DECISION ) {
                    if( !isset($data['final_status']) || !PdoiApplicationStatusesEnum::isFinalStatus($data['final_status']) ) {
                        throw new \Exception('Try to set not final application status: '. PdoiApplicationStatusesEnum::keyByValue((int)$data['final_status']));
                    }
                }
                //TODO Set communication and status
                //TODO Do we save final decision text as application response or it must be empty in some cases
                //TODO Where to show events files in application
            }
        } catch (\Exception $e) {
            logError('Register event ('. $eventConfig->name .') for application ID '.$this->application->id, $e->getMessage());
        }

        return $newEvent;
    }

    private function attachEventFiles($event, $files, $descriptions)
    {
        $userId = auth()->user() ? auth()->user()->id : 0;
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
                'user_reg' => $userId,
            ]);
            $event->files()->save($newFile);
            $ocr = new FileOcr($newFile->refresh());
            $ocr->extractText();
        }
    }
}
