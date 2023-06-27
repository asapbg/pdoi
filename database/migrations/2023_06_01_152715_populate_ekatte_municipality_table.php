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
        DB::table('ekatte_municipality_translations')->truncate();
        DB::table('ekatte_municipality')->truncate();

        $locales = config('available_languages');
        $csvFile = fopen(base_path("database/import_files/ekatte_municipality.csv"), "r");
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if(is_array($data) && sizeof($data) == 8) {
                $item = \App\Models\EkatteMunicipality::create([
                    'id' => $data[0],
                    'obstina' => $data[1],
                    'ekatte' => $data[2],
                    'category' => $data[4],
                    'document' => $data[5],
                    'abc' => $data[6],
                    'valid' => $data[7],
                    'active' => 1
                ]);

                if ($item) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->ime = $data[3];
                    }
                }
                $item->save();
            }
        }

        fclose($csvFile);

        $currentId = DB::table('ekatte_municipality')->max('id') + 1;
        DB::raw('ALTER SEQUENCE ekatte_municipality_id_seq RESTART WITH '.$currentId);
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
