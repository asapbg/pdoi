<?php

namespace Database\Seeders;

use App\Models\Egov\EgovMessageCoresp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EgovMessageCorespSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('egov_message_coresp')->truncate();

        $oldItems = DB::connection('old')->select('
            select
                egov_messages_coresp.id,
                egov_messages_coresp.id_message,
                egov_messages_coresp.ime as name,
                egov_messages_coresp.egn,
                egov_messages_coresp.idcard as id_card,
                egov_messages_coresp.bulstat as eik,
                egov_messages_coresp.city,
                egov_messages_coresp.adres as address,
                egov_messages_coresp.pk,
                egov_messages_coresp.email,
                egov_messages_coresp.phone,
                egov_messages_coresp.mobile_phone,
                egov_messages_coresp.dop_info
            from egov_messages_coresp
            ');

        if( sizeof($oldItems) ) {
            DB::beginTransaction();
            try {
                foreach ($oldItems as $item) {
                    $appToArray = get_object_vars($item);
                    $newItem = new EgovMessageCoresp();
                    $newItem->fill($appToArray);
                    $newItem->save();
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old egov message corespondents: '. $e->getMessage());
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['egov_message_coresp'];
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
