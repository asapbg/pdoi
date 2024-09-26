<?php

use App\Models\Event;
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
        $resendEvent = \App\Models\Event::where('app_event', '=', \App\Enums\ApplicationEventsEnum::FORWARD->value)->first();
        if($resendEvent){
            $resendEvent->nextEvents()->sync([1,4,2,5,6]);
        }

        $resendCholdSubjectEvent = \App\Models\Event::where('app_event', '=', \App\Enums\ApplicationEventsEnum::FORWARD_TO_SUB_SUBJECT->value)->first();
        if($resendCholdSubjectEvent){
            $resendCholdSubjectEvent->nextEvents()->sync([1,4,2,5,6]);
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
