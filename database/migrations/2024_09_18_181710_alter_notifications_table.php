<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement('ALTER TABLE notifications ALTER COLUMN type_channel DROP NOT NULL;');
        DB::statement('ALTER TABLE notifications ALTER COLUMN is_send DROP NOT NULL;');
        DB::statement('ALTER TABLE notifications ALTER COLUMN cnt_send DROP NOT NULL;');
        DB::statement('ALTER TABLE notifications ALTER COLUMN type DROP NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
