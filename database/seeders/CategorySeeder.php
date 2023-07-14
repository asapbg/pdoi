<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_translations')->truncate();
        DB::table('category')->truncate();
        $locales = config('available_languages');

        $oldCategories = DB::connection('old')->select("
            select
                system_classif.code as id,
                max(case WHEN sysclassif_multilang.lang = 1 then sysclassif_multilang.tekst else '' end) as bg,
                max(case WHEN sysclassif_multilang.lang = 2 then sysclassif_multilang.tekst else '' end) as en
            from system_classif
            join sysclassif_multilang on sysclassif_multilang.tekst_key = system_classif.tekst_key
            where
                system_classif.code_classif = 10008 -- categories/themes
            group by system_classif.code, system_classif.code_classif
            order by system_classif.code asc
        ");

        if( sizeof($oldCategories) ) {
            foreach ($oldCategories as $category) {
                $item = Category::create([
                    'id' => $category->id
                ]);

                if ($item) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->name = $category->{$locale['code']} ?? '';
                    }
                }
                $item->save();
            }
        }

        $tableToResetSeq = ['category', 'category_translations'];
        foreach ($tableToResetSeq as $table) {
            \Illuminate\Support\Facades\DB::statement(
                "do $$
                        declare newId int;
                        begin
                            select (coalesce(max(id),0) +1)  from ".$table." into newId;
                            execute 'alter SEQUENCE ".$table."_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
            );
        }
    }
}
