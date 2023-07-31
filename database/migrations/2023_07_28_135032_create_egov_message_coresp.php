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
        Schema::create('egov_message_coresp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_message');
            $table->string('name')->nullable();
            $table->string('egn',50)->nullable();
            $table->string('id_card',50)->nullable();
            $table->string('eik',20)->nullable();
            $table->string('city',50)->nullable();
            $table->string('address')->nullable();
            $table->string('pk', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone',200)->nullable();
            $table->string('mobile_phone', 200)->nullable();
            $table->text('dop_info')->nullable();

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
        Schema::dropIfExists('egov_message_coresp');
    }
};
