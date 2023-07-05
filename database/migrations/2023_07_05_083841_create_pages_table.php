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
        Schema::create('page', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->on('menu_section')->references('id');
            $table->string('slug');
            $table->tinyInteger('order_idx')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedBigInteger('page_id');
            $table->unique(['page_id', 'locale']);
            $table->foreign('page_id')
                ->references('id')
                ->on('page');
            $table->string('name');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('page');
    }
};
