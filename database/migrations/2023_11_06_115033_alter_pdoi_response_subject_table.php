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
        Schema::table('pdoi_response_subject', function (Blueprint $table) {
            $table->unsignedBigInteger('ssev_profile_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdoi_response_subject', function (Blueprint $table) {
            $table->dropColumn('ssev_profile_id');
        });
    }
};
