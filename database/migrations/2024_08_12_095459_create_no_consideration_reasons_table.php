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
        Schema::create('no_consider_reason', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('no_consider_reason_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedBigInteger('no_consider_reason_id');
            $table->unique(['no_consider_reason_id', 'locale']);
            $table->foreign('no_consider_reason_id')
                ->references('id')
                ->on('no_consider_reason');
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
        Schema::dropIfExists('no_consider_reason_translations');
        Schema::dropIfExists('no_consider_reason');
    }
};
