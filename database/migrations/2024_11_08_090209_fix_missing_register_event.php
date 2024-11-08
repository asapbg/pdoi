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
        $data = array(
            [
                'pdoi_application_id' => 12063,
                'event_type' => \App\Enums\ApplicationEventsEnum::MANUAL_REGISTER->value,
                'event_date' => '2024-11-05',
                'user_reg' => 7545,
                'created_at' => '2024-11-05 12:17:21',
                'status' => 1
            ]
        );

        if(sizeof($data)){
            foreach ($data as $event){
                if(\App\Models\PdoiApplication::find($event['pdoi_application_id'])){
                    \App\Models\PdoiApplicationEvent::create($event);
                }
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
