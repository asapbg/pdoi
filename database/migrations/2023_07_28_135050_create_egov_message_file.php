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
        Schema::create('egov_message_file', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_message');
            $table->string('filename', 400)->nullable();
            $table->string('mime', 4000)->nullable();
            $table->string('path')->nullable();
            $table->text('result')->nullable();
            $table->string('has_malware',1)->nullable();
            $table->string('status',25)->nullable();
            $table->integer('ord')->nullable();
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
        Schema::dropIfExists('egov_message_file');
    }
};
