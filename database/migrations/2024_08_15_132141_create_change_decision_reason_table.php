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
        Schema::create('change_decision_reason', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('change_decision_reason_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedBigInteger('change_decision_reason_id');
            $table->unique(['change_decision_reason_id', 'locale']);
            $table->foreign('change_decision_reason_id')
                ->references('id')
                ->on('change_decision_reason');
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
        Schema::dropIfExists('change_decision_reason_translations');
        Schema::dropIfExists('change_decision_reason');
    }
};
