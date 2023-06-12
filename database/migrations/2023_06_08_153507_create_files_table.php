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
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_object');
            $table->integer('code_object'); // ?????
            $table->string('filename', 200)->nullable();
            $table->string('content_type', 500)->nullable();
            $table->binary('content')->nullable();
            $table->string('path')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('visible_on_site')->default(0);

            $table->unsignedBigInteger('user_reg')->nullable();
            $table->foreign('user_reg')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('user_last_mod')->nullable();
            $table->foreign('user_last_mod')
                ->references('id')
                ->on('users');


            $table->timestamps(); //date_reg, date_last_mod
            $table->softDeletes();
        });

        Schema::create('pdoi_files_relation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_object');
            $table->integer('code_object');

            $table->unsignedBigInteger('id_file');
            $table->foreign('id_file')
                ->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdoi_files_relation');
        Schema::dropIfExists('files');
    }
};
