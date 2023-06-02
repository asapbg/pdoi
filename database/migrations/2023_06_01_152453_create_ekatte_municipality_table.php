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
        Schema::create('ekatte_municipality', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('obstina', 50);
            $table->integer('ekatte');
            $table->string('category', 50)->nullable();
            $table->string('document', 50)->nullable();
            $table->string('abc', 50)->nullable();
            $table->string('valid', 1);

            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('ekatte_municipality_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('ekatte_municipality_id');
            $table->unique(['ekatte_municipality_id', 'locale']);
            $table->foreign('ekatte_municipality_id')
                ->references('id')
                ->on('ekatte_municipality');

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
        Schema::dropIfExists('ekatte_municipality_translations');
        Schema::dropIfExists('ekatte_municipality');
    }
};
