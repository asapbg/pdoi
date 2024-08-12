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
        $events = [];
        $finalDecisionEvent = \App\Models\Event::where('app_event', '=', Event::APP_EVENT_FINAL_DECISION)->first();
        if($finalDecisionEvent){
            $events = $finalDecisionEvent->nextEvents->pluck('event_id')->toArray();
            if(sizeof($events)){
                if(!in_array(Event::APP_EVENT_FINAL_DECISION, $events)){
                    $events[] = Event::APP_EVENT_FINAL_DECISION;
                }
            } else{
                $events[] = Event::APP_EVENT_FINAL_DECISION;
            }
            $finalDecisionEvent->nextEvents()->sync($events);
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
