<?php

namespace Database\Seeders;

use App\Models\Egov\EgovOrganisation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EGovOrganisationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('egov_organisation')->truncate();

        $oldItems = DB::connection('old')->select('
            select
                egov_organisations.id,
                egov_organisations.eik,
                egov_organisations.guid,
                egov_organisations.parrent_guid as parent_guid,
                egov_organisations.administrative_body_name,
                egov_organisations.postal_address,
                egov_organisations.web_site,
                egov_organisations.contact,
                egov_organisations.phone,
                egov_organisations.fax,
                egov_organisations.email,
                egov_organisations.sert_sn as cert_sn,
                (case when egov_organisations.status = \'Active\' then 1 else 0 end) as status,
                egov_organisations.url_http,
                egov_organisations.url_https,
                egov_organisations.last_mod_date as updated_at
            from egov_organisations
            ');

        if( sizeof($oldItems) ) {
            DB::beginTransaction();
            try {
                foreach ($oldItems as $item) {
                    $appToArray = get_object_vars($item);
                    $newItem = new EgovOrganisation();
                    $newItem->fill($appToArray);
                    $newItem->save();
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old egov organisations: '. $e->getMessage());
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['egov_organisation'];
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
