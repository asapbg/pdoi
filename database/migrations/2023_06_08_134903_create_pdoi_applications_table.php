<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('pdoi_application', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_reg');
            $table->foreign('user_reg')
                ->references('id')->on('users');

            $table->tinyInteger('applicant_type');
            $table->string('applicant_identity', 20)->nullable(); //егн, личнна карта, еик

            $table->string('email')->nullable();
            $table->string('post_code', 10)->nullable();

            $table->string('full_names', 2000);
            $table->string('headoffice', 2000)->nullable();

            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')
                ->references('id')->on('country');

            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')
                ->references('id')->on('ekatte_area');

            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->foreign('municipality_id')
                ->references('id')->on('ekatte_municipality');

            $table->unsignedBigInteger('settlement_id')->nullable();
            $table->foreign('settlement_id')
                ->references('id')->on('ekatte_settlement');

            $table->string('address', 2000)->nullable();
            $table->string('address_second', 2000)->nullable();
            $table->string('phone', 2000)->nullable();

            $table->integer('response_subject_id');
            $table->foreign('response_subject_id')
                ->references('id')
                ->on('pdoi_response_subject');
            $table->text('response')->nullable();
            $table->timestamp('response_date')->nullable();

            $table->timestamp('registration_date')->nullable();//дата на деловодна регистрация

            $table->text('request');
            $table->tinyInteger('status')->default(\App\Enums\PdoiApplicationStatusesEnum::RECEIVED->value);
            $table->timestamp('status_date')->useCurrent();

            $table->string('application_uri', 2000)->unique();

            $table->integer('replay_in_time')->nullable();
            $table->bigInteger('number_of_visits')->nullable();
            $table->integer('usefulness')->nullable();

            $table->tinyInteger('email_publication')->default(0);
            $table->tinyInteger('names_publication')->default(0);
            $table->tinyInteger('address_publication')->default(0);
            $table->tinyInteger('headoffice_publication')->default(0);
            $table->tinyInteger('phone_publication')->default(0);

            $table->tinyInteger('user_attached_files')->default(0);

            $table->timestamp('response_end_time')->nullable();
            $table->unsignedBigInteger('egov_mess_id')->nullable();
            $table->unsignedBigInteger('app_id_for_view')->nullable();
            $table->unsignedBigInteger('fw_app')->nullable();
            $table->string('add_info', 5000)->nullable();
            $table->unsignedBigInteger('user_last_mod')->nullable();

            $table->timestamps(); //date_reg, date_last_mod
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE pdoi_application ADD COLUMN request_ts_bg tsvector GENERATED ALWAYS AS (to_tsvector('bulgarian', regexp_replace(regexp_replace(request, E'<[^>]+>', '', 'gi'), E'&nbsp;', '', 'g'))) STORED;");
        DB::statement("CREATE INDEX request_ts_bg_idx ON pdoi_application USING GIN (request_ts_bg);");

        Schema::create('pdoi_application_event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pdoi_application_id');
            $table->foreign('pdoi_application_id')
                ->references('id')
                ->on('pdoi_application');
            $table->unsignedTinyInteger('event_type');
            $table->date('event_date')->useCurrent();
            $table->date('event_end_date')->nullable();
            $table->text('add_text')->nullable();
            $table->unsignedBigInteger('old_resp_subject_id')->nullable();
            $table->foreign('old_resp_subject_id')
                ->references('id')
                ->on('pdoi_response_subject');
            $table->unsignedBigInteger('new_resp_subject_id')->nullable();
            $table->foreign('new_resp_subject_id')
                ->references('id')
                ->on('pdoi_response_subject');
            $table->unsignedBigInteger('user_reg');
            $table->foreign('user_reg')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('user_last_mod')->nullable();
            $table->foreign('user_last_mod')
                ->references('id')
                ->on('users');

            $table->tinyInteger('status');
            $table->tinyInteger('event_reason')->nullable();
            $table->tinyInteger('reason_not_approved')->nullable();
            $table->unsignedInteger('app_id_for_view')->nullable();

            $table->timestamps();//date_reg,date_last_mod

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdoi_application_event');
        Schema::dropIfExists('pdoi_application');
    }
};
