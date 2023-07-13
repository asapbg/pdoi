<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('country_translations')->truncate();
        DB::table('country')->truncate();

        $csvFile = fopen(base_path("database/import_files/countries.csv"), "r");
        $firstRow = true;
        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            if($firstRow) {$firstRow = false; continue;}
            if(is_array($data) && sizeof($data) == 3) {
                $item = \App\Models\Country::create([
                    'id' => $data[0]
                ]);

                if( $item ) {
                    $item->translateOrNew('bg')->name = $data[1];
                    $item->translateOrNew('en')->name = $data[2];
                }
                $item->save();
            }
        }

        \Illuminate\Support\Facades\DB::statement(
            "do $$
                        declare newId int;
                        begin
                            select (max(id) +1)  from country into newId;
                            execute 'alter SEQUENCE country_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
        );
    }
}
