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
        Schema::create('egov_organisation', function (Blueprint $table) {
            $table->id();
            $table->string('name',500)->nullable();
            $table->string('eik',15)->nullable();
            $table->string('guid',38)->nullable();
            $table->string('parent_guid',38)->nullable();
            $table->string('postal_address',500)->nullable();
            $table->string('web_site',500)->nullable();
            $table->string('contact',500)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('fax',20)->nullable();
            $table->string('email',100)->nullable();
            $table->string('sert_sn',25)->nullable();
            $table->string('url_http')->nullable();
            $table->string('url_https')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('manual')->default(0);
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
        Schema::dropIfExists('egov_organisation');
    }
};
