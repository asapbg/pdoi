<?php

namespace Database\Seeders;

use App\Models\ReasonRefusal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonRefusalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reason_refusal_translations')->truncate();
        DB::table('reason_refusal')->truncate();
        $locales = config('available_languages');

        //sections
        $data = [
            1 => [ 'bg' => 'Информацията е класифициранаили друга защитена тайна', 'en' => 'Информацията е класифициранаили друга защитена тайна'],
            2 => [ 'bg' => 'Информацията засяга интереситена трето лице, което е отказало предоставяне', 'en' => 'Информацията засяга интереситена трето лице, което е отказало предоставяне'],
            3 => [ 'bg' => 'Информацията е предоставена на заявителя през предходните 6 месеца', 'en' => 'Информацията е предоставена на заявителя през предходните 6 месеца'],
        ];

        foreach ($data as $id => $dbItem) {
            $item = ReasonRefusal::create([
                'id' => $id
            ]);

            if ($item) {
                foreach ($locales as $locale) {
                    $item->translateOrNew($locale['code'])->name = $dbItem[$locale['code']] ?? '';
                }
            }
            $item->save();
        }

        $tableToResetSeq = ['reason_refusal'];
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
}
