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
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('password');

            $table->tinyInteger('user_type')->default(1);//external/internal
            $table->integer('profile_type')->default(0);//profile type depending on personal or company
            $table->unsignedBigInteger('administrative_unit')->nullable();//internal user rzs

            $table->string('names')->nullable();
            $table->tinyInteger('lang')->default(1);
            $table->string('email')->nullable();
            $table->string('person_identity', 20)->nullable();
            $table->string('company_identity', 20)->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('country');

            $table->unsignedBigInteger('ekatte_municipality_id')->nullable();
            $table->foreign('ekatte_municipality_id')->references('id')->on('ekatte_municipality');

            $table->unsignedBigInteger('ekatte_area_id')->nullable();
            $table->foreign('ekatte_area_id')->references('id')->on('ekatte_area');

            $table->unsignedBigInteger('ekatte_settlement_id')->nullable();
            $table->foreign('ekatte_settlement_id')->references('id')->on('ekatte_settlement');

            $table->string('post_code',10)->nullable();
            $table->string('address')->nullable();
            $table->string('address_second')->nullable();

            $table->tinyInteger('delivery_method')->nullable();

            $table->string('phone', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('status_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status_explain')->nullable();
            $table->integer('org_id')->nullable();
            $table->integer('org_code')->nullable();
            $table->string('org_text')->nullable();
            $table->integer('position')->nullable();
            $table->string('position_text')->nullable();
            $table->integer('mail_forward')->default(0);
            $table->string('mail_user')->nullable();
            $table->string('mail_pass')->nullable();
            $table->string('mail_email')->nullable();
            $table->string('mail_imap_server')->nullable();
            $table->string('mail_smtp_server')->nullable();
            $table->unsignedBigInteger('user_reg')->nullable(); //who create user
            $table->unsignedBigInteger('user_last_mod')->nullable();  //who modify, last user
            $table->string('reg_token')->nullable();
            $table->timestamp('reg_token_expire')->nullable();
            $table->string('pass_rec_token')->nullable();
            $table->timestamp('pass_rec_expire')->nullable();
            $table->tinyInteger('login_attempts')->default(0);
            $table->timestamp('pass_last_change')->nullable();
            $table->tinyInteger('pass_is_new')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->tinyInteger('active')->default(1);

            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
