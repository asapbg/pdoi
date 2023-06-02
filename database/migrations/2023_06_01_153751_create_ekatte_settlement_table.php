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
        Schema::create('ekatte_settlement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ekatte');
            $table->string('tvm', 50)->nullable();
            $table->string('oblast', 50)->nullable();
            $table->string('obstina', 50)->nullable();
            $table->string('kmetstvo', 50)->nullable();
            $table->string('kind', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->string('altitude', 50)->nullable();
            $table->string('document', 50)->nullable();
            $table->string('tsb', 50)->nullable();
            $table->string('abc', 50)->nullable();
            $table->string('valid', 1);

            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('ekatte_settlement_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('ekatte_settlement_id');
            $table->unique(['ekatte_settlement_id', 'locale']);
            $table->foreign('ekatte_settlement_id')
                ->references('id')
                ->on('ekatte_settlement');

            $table->string('ime', 200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ekatte_settlement_translations');
        Schema::dropIfExists('ekatte_settlement');
    }
};
