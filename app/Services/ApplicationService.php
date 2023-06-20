<?php

namespace App\Services;

use App\Enums\ApplicationEventsEnum;
use App\Models\Event;
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
            $eventConfig = match ($eventType) {
                ApplicationEventsEnum::SEND->value => Event::where('app_event', $eventType)->first(),
            };

            if( $eventConfig ) {
                $newEvent = new PdoiApplicationEvent();
                $newEvent->event_type = ApplicationEventsEnum::SEND->value;
                $newEvent->event_date = Carbon::now();
                if ($eventConfig->days) {
                    $newEvent->event_end_date = Carbon::parse($this->application->response_end_time)->addDays($eventConfig->days);
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
                $newEvent->user_reg = $this->user;
                $newEvent->status = $eventConfig->event_status;
                $this->application->events()->save($newEvent);
                $newEvent->refresh();
            }
        } catch (\Exception $e) {
            logError('Register event ('. ApplicationEventsEnum::SEND->name .')', $e->getMessage());
        }

        return $newEvent;
    }
}
