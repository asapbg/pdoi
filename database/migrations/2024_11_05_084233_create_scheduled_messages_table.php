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
        Schema::create('scheduled_message', function (Blueprint $table) {
            $table->id();
            $table->string('type', 500);
            $table->tinyInteger('by_email')->default(0);
            $table->tinyInteger('by_app')->default(0);
            $table->timestamp('start_at');
            $table->jsonb('data');
            $table->jsonb('send_to');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('is_send')->default(0);
            $table->timestamp('send_at')->nullable();
            $table->jsonb('not_send_to_by_email')->nullable();
            $table->jsonb('not_send_to_by_app')->nullable();
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
        Schema::dropIfExists('scheduled_message');
    }
};
