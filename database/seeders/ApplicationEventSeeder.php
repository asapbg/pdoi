<?php

namespace Database\Seeders;

use App\Models\PdoiApplicationEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pdoi_application_event')->truncate();

        $oldEvents = DB::connection('old')->select('
            select
                pdoi_event.id,
                pdoi_event.application_id as pdoi_application_id,
                pdoi_event.event_type,
                pdoi_event.event_date,
                pdoi_event.status,
                pdoi_event.event_end_date,
                pdoi_event.add_text,
                pdoi_event.event_reason,
                pdoi_event.reason_not_approved,
                pdoi_event.old_resp_subject_id,
                pdoi_event.new_resp_subject_id,
                case when pdoi_event.user_reg = -100 then null else adm_users.user_id end as user_reg, -- system user and fix for not existing users by setting them as system
                pdoi_event.date_reg as created_at,
                pdoi_event.user_last_mod,
                pdoi_event.date_last_mod as updated_at,
                pdoi_event.app_id_for_view
            from pdoi_event
            left join adm_users on adm_users.user_id = pdoi_event.user_reg
            where pdoi_event.id not in (1685,1686) -- events with not existing applications
                and pdoi_event.application_id not in (6554,6743,7016,7021,7033,6647,7020,7087,7088,7086, 328) -- this applications are skipped by ApplicationSeeder because they do not have user
            order by pdoi_event.id
            ');

        if( sizeof($oldEvents) ) {
            DB::beginTransaction();
            try {
                $oldEventsChunks = array_chunk($oldEvents, 50);
                foreach ($oldEventsChunks as $events) {
                    if( sizeof($events) ) {
                        foreach ($events as $event) {
                            $itemToArray = get_object_vars($event);
                            $newItem = new PdoiApplicationEvent();
                            $newItem->fill($itemToArray);
                            $newItem->save();
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old application events: '. $e);
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['pdoi_application_event'];
        foreach ($tableToResetSeq as $table) {
            \Illuminate\Support\Facades\DB::statement(
                "do $$
                        declare newId int;
                        begin
                            select (coalesce(max(id),0) +1)  from ".$table." into newId;
                            execute 'alter SEQUENCE ".$table."_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
            );
        }
    }
}
