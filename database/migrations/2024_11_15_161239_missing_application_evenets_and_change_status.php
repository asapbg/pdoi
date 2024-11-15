<?php

use App\Models\PdoiApplication;
use Carbon\Carbon;
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
        $appId = 12036;
        $appStatus = \App\Enums\PdoiApplicationStatusesEnum::IN_PROCESS->value;

        $data = array(
            [
                'pdoi_application_id' => 12036,
                'event_type' => \App\Enums\ApplicationEventsEnum::SEND_TO_SEOS->value,
                'event_date' => '2024-11-01',
                'created_at' => '2024-11-01 14:18:00',
                'status' => 1
            ],
            [
                'pdoi_application_id' => 12036,
                'event_type' => \App\Enums\ApplicationEventsEnum::APPROVE_BY_SEOS->value,
                'event_date' => '2024-11-01',
                'created_at' => '2024-11-01 16:00',
                'status' => 1
            ]
        );

        $app = \App\Models\PdoiApplication::find($appId);
        if($app){
            if(sizeof($data)){
                foreach ($data as $event){
                    \App\Models\PdoiApplicationEvent::create($event);
                }
            }
            $app->status = $appStatus;
            $app->registration_date = '2024-11-01 00:00:00';
            $app->response_end_time = Carbon::parse('2024-11-01 00:00:00')->addDays(PdoiApplication::DAYS_AFTER_SUBJECT_REGISTRATION)->endOfDay();
            $app->save();
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
