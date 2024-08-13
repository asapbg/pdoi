<?php

namespace App\Console\Commands;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\PdoiApplication;
use App\Models\PdoiApplicationEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixApplicationLastEventStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:app_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix application status based on last event type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = DB::select('
            select
                pa.id as app_id,
                pae.id as app_event_id
                -- pa.application_uri,
                -- pae.*
            from pdoi_application pa
            join pdoi_application_event pae on pae.pdoi_application_id = pa.id
            where true
                and pa.status = '.PdoiApplicationStatusesEnum::NO_REVIEW->value.' -- //Просрочено
                and pae.event_type = '.ApplicationEventsEnum::FINAL_DECISION->value.' -- //Крайно решение
            order by pa.id
        ');

        if(sizeof($data)){
            $applicationIds = array_column($data, 'app_id');
            $appEventIds = array_column($data, 'app_event_id');

            DB::beginTransaction();
            try {
                if(sizeof($applicationIds)){
                    PdoiApplication::whereIn('id', $applicationIds)->update(['status' => PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value]);
                    PdoiApplicationEvent::whereIn('id', $appEventIds)->update(['event_reason' => PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value]);
                }
                DB::commit();
            } catch (\Exception $e){
                DB::rollBack();
                Log::error('Command \'fix:app_status\' error: '.$e);
            }
        }
        return Command::SUCCESS;
    }
}
