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
        Schema::table('pdoi_application_event', function (Blueprint $table){
            $table->string('subject_eik',13)->nullable();
            $table->string('subject_name')->nullable();
        });

        Schema::table('pdoi_application', function (Blueprint $table){
            $table->string('not_registered_subject_eik',13)->nullable();
            $table->string('not_registered_subject_name')->nullable();
            $table->integer('response_subject_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdoi_application_event', function (Blueprint $table){
            $table->dropColumn('subject_eik',13);
            $table->dropColumn('subject_name');
        });

        Schema::table('pdoi_application', function (Blueprint $table){
            $table->dropColumn('not_registered_subject_eik',13);
            $table->dropColumn('not_registered_subject_name');
        });
    }
};
