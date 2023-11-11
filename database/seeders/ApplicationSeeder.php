<?php

namespace Database\Seeders;

use App\Models\EkatteSettlement;
use App\Models\PdoiApplication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pdoi_application_category')->truncate();
        DB::table('pdoi_application')->truncate();

        $oldApplications = DB::connection('old')->select('
            select
                pdoi_application.id,
                -- !!! Detect if applicant uploaded files
                0 as user_attached_files,
                -- themes
                array_agg(pdoi_app_themes.theme_value) FILTER ( WHERE pdoi_app_themes.theme_value is not null ) as categories,

                pdoi_application.user_reg,
                pdoi_application.applicant_type,
                pdoi_application.email,
                pdoi_application.post_code,
                pdoi_application.full_names,
                pdoi_application.headoffice,
                pdoi_application.country as country_id,
                pdoi_application.region as area_id,
                pdoi_application.municipality as municipality_id,
                pdoi_application.town as settlement_id,
                pdoi_application.address,
                pdoi_application.phone,
                pdoi_application.response_subject_id,
                pdoi_application.request,
                pdoi_application.status,
                pdoi_application.status_date,
                pdoi_application.application_uri,
                pdoi_application.registration_date,
                pdoi_application.response,
                pdoi_application.response_date,
                pdoi_application.replay_in_time,
                pdoi_application.number_of_visits,
                pdoi_application.usefulness,
                pdoi_application.email_publication,
                pdoi_application.names_publication,
                pdoi_application.address_publication,
                pdoi_application.headoffice_publication,
                pdoi_application.phone_publication,
                pdoi_application.response_end_time,
                pdoi_application.date_reg as created_at,
                pdoi_application.date_last_mod as updated_at,

                -- do we need this column

                pdoi_application.fw_app,
                pdoi_application.egov_mess_id,
                pdoi_application.app_id_for_view,
                pdoi_application.doc_guid,
                pdoi_application.add_info,
                pdoi_application.user_last_mod
            from pdoi_application
            left join pdoi_app_themes on pdoi_app_themes.application_id = pdoi_application.id
                where pdoi_application.id not in (6554,6743,7016,7021,7033,6647,7020,7087,7088,7086, 328) -- application with not existing user_reg
            group by pdoi_application.id
            order by pdoi_application.id asc
            ');

        if( sizeof($oldApplications) ) {
            $settlements = EkatteSettlement::get()->pluck('id', 'ekatte')->toArray();
            DB::beginTransaction();
            try {
                foreach ($oldApplications as $app) {
                    $categories = !empty($app->categories) ? explode(',', trim($app->categories, '{}')) : [];

                    //FIX There is one record with wrong subject and should be 'Министерски съвет и неговата администрация'
                    if( $app->response_subject_id == -6 ) {
                        $app->response_subject_id = 12697;
                    }
                    //get settlement id, because from old db we get it as ekatte number
                    $app->settlement_id = $settlements[$app->settlement_id] ?? null;

                    $appToArray = get_object_vars($app);
                    unset($appToArray['categories']);

                    $newApp = new PdoiApplication();
                    $newApp->fill($appToArray);
                    $newApp->save();
                    if( sizeof($categories) ){
                        $newApp->categories()->attach($categories);
                    }
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old application: '. $e);
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['pdoi_application'];
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
