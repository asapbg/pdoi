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
        Schema::create('pdoi_response_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('eik',13)->nullable();
            $table->unsignedBigInteger('region')->nullable();
            $table->unsignedBigInteger('municipality')->nullable();
            $table->unsignedBigInteger('town')->nullable();
            $table->string('phone',1000)->nullable();
            $table->string('fax',1000)->nullable();
            $table->string('email')->nullable();
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->tinyInteger('adm_register')->default(1);
            $table->tinyInteger('redirect_only');

            $table->unsignedBigInteger('adm_level')->nullable();
            $table->foreign('adm_level')
                ->references('id')
                ->on('pdoi_response_subject');

            $table->integer('zip_code')->nullable();
            $table->string('nomer_register', 25)->index()->nullable();
            $table->tinyInteger('active')->default(1);

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
        Schema::dropIfExists('pdoi_response_subject');
    }
};
