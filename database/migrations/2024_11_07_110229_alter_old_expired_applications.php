<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sendToSubjectIds = array();
        $sendToSubject = \Illuminate\Support\Facades\DB::select('
            select
                A.id
            from (
            select
                pa.id,
                pa.application_uri,
                pa.created_at,
                pa.response_end_time,
                (
                    select case when count(id) > 0 then 2 else 1 end
                            from pdoi_application_event
                            where
                                pdoi_application_event.pdoi_application_id = pa.id
                                and pdoi_application_event.event_type = 7 -- Изпратено към деловодната система
                        ) as new_status,
                        pa.status as old_status
                    from pdoi_application as pa
                    left join pdoi_application_event as pae on pae.pdoi_application_id = pa.id
                        and (pae.event_type <> 9 and pae.event_type <> 7) -- Прието на платформата, Изпратено към деловодната система
                    where true
                        and pa.status = 8 -- Непроизнесено в срок
                        and pa.manual = 0 -- не са ръчно вписани
                        and pae.id is null -- нямат други регистрирани събития
                    group by pa.id
                    order by pa.id
            ) A
            where A.new_status = 2
        ');

        if(sizeof($sendToSubject)){
            foreach ($sendToSubject as $row){
                $sendToSubjectIds[] = $row->id;
            }

            if(sizeof($sendToSubjectIds)){
                \Illuminate\Support\Facades\DB::statement('
                    update pdoi_application
                    set status = 2
                    where
                        pdoi_application.status = 8
                        and pdoi_application.id in ('.implode(',', $sendToSubjectIds).')
                ');
            }
        }

        $receivedOnPlatformIds = [];
        $receivedOnPlatform = \Illuminate\Support\Facades\DB::select('
            select
                A.id
            from (
            select
                pa.id,
                pa.application_uri,
                pa.created_at,
                pa.response_end_time,
                (
                    select case when count(id) > 0 then 2 else 1 end
                            from pdoi_application_event
                            where
                                pdoi_application_event.pdoi_application_id = pa.id
                                and pdoi_application_event.event_type = 7 -- Изпратено към деловодната система
                        ) as new_status,
                        pa.status as old_status
                    from pdoi_application as pa
                    left join pdoi_application_event as pae on pae.pdoi_application_id = pa.id
                        and (pae.event_type <> 9 and pae.event_type <> 7) -- Прието на платформата, Изпратено към деловодната система
                    where true
                        and pa.status = 8 -- Непроизнесено в срок
                        and pa.manual = 0 -- не са ръчно вписани
                        and pae.id is null -- нямат други регистрирани събития
                    group by pa.id
                    order by pa.id
            ) A
            where A.new_status = 1
        ');

        if(sizeof($receivedOnPlatform)){
            foreach ($receivedOnPlatform as $row){
                $receivedOnPlatformIds[] = $row->id;
            }

            if(sizeof($receivedOnPlatformIds)){
                \Illuminate\Support\Facades\DB::statement('
                    update pdoi_application
                    set status = 1
                    where
                        pdoi_application.status = 8
                        and pdoi_application.id in ('.implode(',', $receivedOnPlatformIds).')
                ');
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
