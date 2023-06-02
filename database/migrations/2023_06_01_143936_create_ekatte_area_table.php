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
        Schema::create('ekatte_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('oblast', 50);
            $table->integer('ekatte');
            $table->string('region', 50);
            $table->string('document', 50)->nullable();
            $table->string('abc', 50)->nullable();
            $table->string('valid', 1);

            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('ekatte_area_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('ekatte_area_id');
            $table->unique(['ekatte_area_id', 'locale']);
            $table->foreign('ekatte_area_id')
                ->references('id')
                ->on('ekatte_area');

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
        Schema::dropIfExists('ekatte_area_translations');
        Schema::dropIfExists('ekatte_area');
    }
};
