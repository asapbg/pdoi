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
        Schema::create('pdoi_response_subject_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('pdoi_response_subject_id');
            $table->unique(['pdoi_response_subject_id', 'locale']);
            $table->foreign('pdoi_response_subject_id')
                ->references('id')
                ->on('pdoi_response_subject');

            $table->string('subject_name');
            $table->string('address')->nullable();
            $table->string('add_info', 500)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdoi_response_subject_translations');
    }
};
