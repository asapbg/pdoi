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
        Schema::table('country', function (Blueprint $table) {
            $table->tinyInteger('is_default')->default(0);
        });

        if(\App\Models\Country::count() > 0){
            $default = \App\Models\Country::whereHas('translation', function ($q){
                $q->where('name', 'ilike', '%българия%');
            })->first();

            if($default){
                $default->update(['is_default' => 1]);
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
        Schema::table('country', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
