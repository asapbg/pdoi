<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('ekatte_area_translations')->truncate();
        DB::table('ekatte_area')->truncate();

        $locales = config('available_languages');
        $csvFile = fopen(base_path("database/import_files/ekatte_area.csv"), "r");
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if(is_array($data) && sizeof($data) == 8) {
                $item = \App\Models\EkatteArea::create([
                    'id' => $data[0],
                    'oblast' => $data[1],
                    'ekatte' => $data[2],
                    'region' => $data[4],
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
        $currentId = DB::table('ekatte_area')->max('id') + 1;
        DB::raw('ALTER SEQUENCE ekatte_area_id_seq RESTART WITH '.$currentId);
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
