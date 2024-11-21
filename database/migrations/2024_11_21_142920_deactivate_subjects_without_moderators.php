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
        $subjects = \App\Models\PdoiResponseSubject::IsActive()->get();
        if($subjects->count()){
            foreach ($subjects as $s){
                if($s->activeUsers->count() == 0){
                    $s->active = 0;
                    $s->save();
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
