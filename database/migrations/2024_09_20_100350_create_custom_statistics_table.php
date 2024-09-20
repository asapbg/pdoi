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
        Schema::create('custom_statistic', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->timestamp('publish_from');
            $table->timestamp('publish_to')->nullable();
            $table->json('data');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('custom_statistic_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->unsignedBigInteger('custom_statistic_id');
            $table->unique(['custom_statistic_id', 'locale']);
            $table->foreign('custom_statistic_id')
                ->references('id')
                ->on('custom_statistic');
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
        Schema::dropIfExists('custom_statistic_translations');
        Schema::dropIfExists('custom_statistics');
    }
};
