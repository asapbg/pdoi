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
        Schema::create('extend_terms_reason', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('extend_terms_reason_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedBigInteger('extend_terms_reason_id');
            $table->unique(['extend_terms_reason_id', 'locale']);
            $table->foreign('extend_terms_reason_id')
                ->references('id')
                ->on('extend_terms_reason');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extend_terms_reason_translations');
        Schema::dropIfExists('extend_terms_reason');
    }
};
