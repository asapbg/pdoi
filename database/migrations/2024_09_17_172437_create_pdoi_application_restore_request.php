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
        Schema::create('pdoi_application_restore_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pdoi_application_id');
            $table->foreign('pdoi_application_id')->references('id')->on('pdoi_application');
            $table->unsignedBigInteger('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('users');
            $table->text('user_request')->nullable();
            $table->tinyInteger('status')->default(\App\Models\PdoiApplicationRestoreRequest::STATUS_IN_PROCESS);
            $table->timestamp('status_datetime');
            $table->text('reason_refuse')->nullable();
            $table->unsignedBigInteger('status_user_id')->nullable();
            $table->foreign('status_user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdoi_application_restore_request');
    }
};
