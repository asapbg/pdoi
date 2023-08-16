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
        DB::statement('update users set delivery_method = '.\App\Enums\DeliveryMethodsEnum::EMAIL->value);
        DB::statement('update pdoi_response_subject set delivery_method = '.\App\Enums\PdoiSubjectDeliveryMethodsEnum::EMAIL->value);
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
