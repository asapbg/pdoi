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
        $tableToResetSeq = ['ekatte_area', 'ekatte_municipality', 'ekatte_settlement', 'category', 'extend_terms_reason', 'reason_refusal'];
        foreach ($tableToResetSeq as $table) {
            \Illuminate\Support\Facades\DB::statement(
                "do $$
                        declare newId int;
                        begin
                            select (max(id) +1)  from ".$table." into newId;
                            execute 'alter SEQUENCE ".$table."_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
            );
        }
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
