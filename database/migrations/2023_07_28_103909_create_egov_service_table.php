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
        Schema::create('egov_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_org');
            $table->string('service_name', 500)->nullable();
            $table->string('uri', 500)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('tip', 500)->nullable();
            $table->smallInteger('version');
            $table->string('guid', 100)->nullable();
            $table->string('selected', 1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('egov_service');
    }
};
