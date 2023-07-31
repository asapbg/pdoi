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
        Schema::create('egov_message', function (Blueprint $table) {
            $table->id();
            $table->string('msg_guid', 38)->nullable();
            $table->string('sender_guid', 38)->nullable();
            $table->string('sender_name', 2000)->nullable();
            $table->string('recipient_guid', 38)->nullable();
            $table->string('recipient_name', 2000)->nullable();
            $table->string('msg_type')->nullable();
            $table->string('msg_status')->nullable();
            $table->timestamp('msg_status_dat')->nullable();
            $table->timestamp('msg_reg_dat')->nullable();
            $table->text('msg_comment')->nullable();
            $table->smallInteger('msg_urgent')->nullable();
            $table->smallInteger('msg_inout')->nullable();
            $table->string('msg_version')->nullable();
            $table->string('msg_rn')->nullable();
            $table->timestamp('msg_rn_dat')->nullable();
            $table->string('doc_guid', 38)->nullable();
            $table->timestamp('doc_dat')->nullable();
            $table->string('doc_rn')->nullable();
            $table->string('doc_uri_reg')->nullable();
            $table->string('doc_uri_batch')->nullable();
            $table->string('doc_vid')->nullable();
            $table->text('doc_subject')->nullable();
            $table->timestamp('doc_deadline')->nullable();
            $table->string('doc_to')->nullable();
            $table->string('parent_guid', 38)->nullable();
            $table->string('parent_rn')->nullable();
            $table->timestamp('parent_date')->nullable();
            $table->string('parent_uri_reg')->nullable();
            $table->string('parent_uri_batch')->nullable();
            $table->text('doc_comment')->nullable();
            $table->smallInteger('comm_status')->nullable();
            $table->text('comm_error')->nullable();
            $table->text('msg_xml')->nullable();
            $table->string('sender_eik', 25)->nullable();
            $table->string('recipient_eik', 25)->nullable();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->text('reason')->nullable();
            $table->integer('ord')->nullable();
            $table->string('has_malware', 1)->nullable();
            $table->string('source')->nullable();
            $table->string('reply_ident')->nullable();
            $table->string('recipient_type')->nullable();

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
        Schema::dropIfExists('egov_message');
    }
};
