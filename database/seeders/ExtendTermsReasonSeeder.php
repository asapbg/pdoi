<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ExtendTermsReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtendTermsReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('extend_terms_reason_translations')->truncate();
        DB::table('extend_terms_reason')->truncate();
        $locales = config('available_languages');

        //sections
        $data = [
            1 => [ 'bg' => 'Удължаване на срока поради запитване до трето лице', 'en' => 'Удължаване на срока поради запитване до трето лице'],
            2 => [ 'bg' => 'Удължаване на срока поради голямо количество информация', 'en' => 'Удължаване на срока поради голямо количество информация'],
        ];

        foreach ($data as $id => $dbItem) {
            $item = ExtendTermsReason::create([
                'id' => $id
            ]);

            if ($item) {
                foreach ($locales as $locale) {
                    $item->translateOrNew($locale['code'])->name = $dbItem[$locale['code']] ?? '';
                }
            }
            $item->save();
        }

        $tableToResetSeq = ['extend_terms_reason'];
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
