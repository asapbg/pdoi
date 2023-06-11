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
                ->references('id')->on('pdoi_response_subject');
            $table->timestamp('registration_date')->nullable();//to subject

            $table->text('request');
            $table->tinyInteger('status')->default(\App\Enums\PdoiApplicationStatusesEnum::RECEIVED->value);
            $table->timestamp('status_date')->useCurrent();

            $table->string('application_uri', 2000)->unique();

            $table->text('response')->nullable();
            $table->timestamp('response_date')->nullable();

            $table->integer('replay_in_time')->nullable();
            $table->bigInteger('number_of_visits')->nullable();
            $table->integer('usefulness')->nullable();

            $table->tinyInteger('email_publication')->default(0);
            $table->tinyInteger('names_publication')->default(0);
            $table->tinyInteger('address_publication')->default(0);
            $table->tinyInteger('headoffice_publication')->default(0);
            $table->tinyInteger('phone_publication')->default(0);

            $table->timestamp('response_end_time')->nullable();
            $table->string('38')->nullable();
            $table->unsignedBigInteger('egov_mess_id')->nullable();
            $table->unsignedBigInteger('app_id_for_view')->nullable();
            $table->unsignedBigInteger('fw_app')->nullable();
            $table->string('add_info', 5000)->nullable();
            $table->unsignedBigInteger('user_last_mod')->nullable();

            $table->timestamps(); //date_reg, date_last_mod
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
        Schema::dropIfExists('pdoi_application');
    }
};
