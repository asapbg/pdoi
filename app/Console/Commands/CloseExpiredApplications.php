<?php

namespace App\Console\Commands;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\PdoiApplication;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseExpiredApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:expired_application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check application with expired end date and expired event "Ask for information" and set correct status (оставено без разглеждане)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredApplication = PdoiApplication::with(['currentEvent'])->ExpiredAndActive()->get();
        if( $expiredApplication->count() ) {
            $needToFinishIds = [];
            foreach ($expiredApplication as $application) {
                //if current event is ask for info and his end time is expired then set application as finished
                if($application->currentEvent && $application->currentEvent->event_type == ApplicationEventsEnum::ASK_FOR_INFO->value) {
                    if( $application->currentEvent->event_end_date <= Carbon::now() ) {
                        $needToFinishIds[]= $application->id;
                    }
                    continue;
                }
                if($application->status == PdoiApplicationStatusesEnum::FORWARDED->value) {
                    continue;
                }
                $needToFinishIds[]= $application->id;
            }
            $applications = array_chunk($needToFinishIds, 10);
            foreach ($applications as $ids) {
                PdoiApplication::whereIn('id', $ids)->update(['status' => PdoiApplicationStatusesEnum::NO_REVIEW->value]);
            }
        }
    }
}
