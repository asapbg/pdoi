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
        DB::table('ekatte_settlement_translations')->truncate();
        DB::table('ekatte_settlement')->truncate();

        $locales = config('available_languages');
        $csvFile = fopen(base_path("database/import_files/ekatte_settlement.csv"), "r");
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if(is_array($data) && sizeof($data) == 13) {
                $item = \App\Models\EkatteSettlement::create([
                    'ekatte' => $data[0],
                    'tvm' => $data[1],
                    'oblast' => $data[3],
                    'obstina' => $data[4],
                    'kmetstvo' => $data[5],
                    'kind' => $data[6],
                    'category' => $data[7],
                    'altitude' => $data[8],
                    'document' => $data[8],
                    'tsb' => $data[10],
                    'abc' => $data[11],
                    'valid' => $data[12],
                    'active' => 1
                ]);

                if( $item ) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->ime = $data[2];
                    }
                }
                $item->save();
            }
        }

        fclose($csvFile);
        $currentId = DB::table('ekatte_settlement')->max('id') + 1;
        DB::raw('ALTER SEQUENCE ekatte_settlement_id_seq RESTART WITH '.$currentId);
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
