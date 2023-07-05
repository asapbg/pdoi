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
        Schema::create('menu_section', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('active')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->on('menu_section')->references('id');
            $table->string('slug');
            $table->tinyInteger('order_idx')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menu_section_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedBigInteger('menu_section_id');
            $table->unique(['menu_section_id', 'locale']);
            $table->foreign('menu_section_id')
                ->references('id')
                ->on('menu_section');
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
        Schema::dropIfExists('menu_section_translations');
        Schema::dropIfExists('menu_section');
    }
};
