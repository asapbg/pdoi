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
        Schema::create('rzs_section', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('adm_level')->nullable();
            $table->string('system_name');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rzs_section_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('rzs_section_id');
            $table->unique(['rzs_section_id', 'locale']);
            $table->foreign('rzs_section_id')
                ->references('id')
                ->on('rzs_section');

            $table->string('name', 255);
        });

        Schema::create('pdoi_response_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('eik',13)->nullable();
            $table->unsignedBigInteger('region')->nullable();
            $table->unsignedBigInteger('municipality')->nullable();
            $table->unsignedBigInteger('town')->nullable();
            $table->string('phone',1000)->nullable();
            $table->string('fax',1000)->nullable();
            $table->string('email')->nullable();
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->tinyInteger('adm_register')->default(1);
            $table->tinyInteger('redirect_only')->default(0);

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')
                ->references('id')
                ->on('pdoi_response_subject');

            $table->unsignedBigInteger('adm_level');
            $table->foreign('adm_level')
                ->references('id')
                ->on('rzs_section');

            $table->integer('zip_code')->nullable();
            $table->string('nomer_register', 25)->index()->nullable();
            $table->tinyInteger('active')->default(1);

            $table->tinyInteger('delivery_method')->default(\App\Enums\PdoiSubjectDeliveryMethodsEnum::EMAIL->value);
            $table->unsignedBigInteger('court_id')->nullable();
            $table->foreign('court_id')
                ->references('id')
                ->on('pdoi_response_subject');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pdoi_response_subject_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('locale')->index();
            $table->unsignedInteger('pdoi_response_subject_id');
            $table->unique(['pdoi_response_subject_id', 'locale']);
            $table->foreign('pdoi_response_subject_id')
                ->references('id')
                ->on('pdoi_response_subject');

            $table->string('subject_name');
            $table->string('address')->nullable();
            $table->string('add_info', 500)->nullable();
            $table->string('court_text')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rzs_section_translations');
        Schema::dropIfExists('rzs_section');
        Schema::dropIfExists('pdoi_response_subject_translations');
        Schema::dropIfExists('pdoi_response_subject');
    }
};
