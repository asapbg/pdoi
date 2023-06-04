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
        Schema::create('profile_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('user_legal_form')->default(0);
            $table->timestamps();
        });

        Schema::create('profile_type_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('profile_type_id');
            $table->unique(['profile_type_id', 'locale']);
            $table->foreign('profile_type_id')
                ->references('id')
                ->on('profile_type');

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
        Schema::dropIfExists('profile_type_translations');
        Schema::dropIfExists('profile_type');
    }
};
