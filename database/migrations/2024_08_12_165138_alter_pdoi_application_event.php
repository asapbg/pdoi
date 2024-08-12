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
        Schema::table('pdoi_application_event', function (Blueprint $table) {
            $table->text('edit_final_decision_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdoi_application_event', function (Blueprint $table) {
            $table->dropColumn('edit_final_decision_reason');
        });
    }
};
