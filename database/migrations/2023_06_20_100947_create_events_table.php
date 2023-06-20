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
        Schema::create('event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('app_event');
            $table->tinyInteger('app_status')->nullable(); //application status after event ???
            $table->tinyInteger('extend_terms_reason_id')->nullable();
            $table->tinyInteger('days')->nullable();
            $table->tinyInteger('date_type')->nullable(); //we do not use this
            $table->tinyInteger('old_resp_subject');
            $table->tinyInteger('new_resp_subject');
            $table->tinyInteger('event_status'); // In old database{1 - Изпълнено, 2 - Неизпълнено}
            $table->bigInteger('reason_not_approved')->nullable();  //we do not use this
            $table->tinyInteger('add_text');
            $table->tinyInteger('files');
            $table->tinyInteger('event_delete');
            $table->tinyInteger('mail_to_admin');
            $table->tinyInteger('mail_to_app');
            $table->tinyInteger('mail_to_new_admin');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('event_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('event_id');
            $table->unique(['event_id', 'locale']);
            $table->foreign('event_id')
                ->references('id')
                ->on('event');

            $table->string('name');
        });

        Schema::create('event_next', function (Blueprint $table) {
            $table->tinyInteger('event_id');
            $table->tinyInteger('event_app_event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_translations');
        Schema::dropIfExists('event_next');
        Schema::dropIfExists('event');
    }
};
