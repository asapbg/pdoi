<?php

namespace Database\Seeders;

use App\Models\Egov\EgovService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EGovServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('egov_service')->truncate();

        $oldItems = DB::connection('old')->select('
            select
                egov_services.id,
                egov_services.id_org,
                egov_services.service_name,
                egov_services.uri,
                (case when egov_services.status = \'Active\' then 1 else 0 end) as status,
                egov_services.tip,
                egov_services.version,
                egov_services.guid,
                egov_services.selected
            from egov_services
            ');

        if( sizeof($oldItems) ) {
            DB::beginTransaction();
            try {
                foreach ($oldItems as $item) {
                    $appToArray = get_object_vars($item);
                    $newItem = new EgovService();
                    $newItem->fill($appToArray);
                    $newItem->save();
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old egov organisations: '. $e);
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['egov_service'];
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
