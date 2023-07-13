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

        //sections
        $data = [
            1 => [ 'bg' => 'Бизнес среда', 'en' => 'Business environment'],
            2 => [ 'bg' => 'Външна политика', 'en' => 'Foreign policy'],
            3 => [ 'bg' => 'Сигурност и отбрана', 'en' => 'Security and Defense'],
            4 => [ 'bg' => 'Държавна администрация', 'en' => 'Public administration'],
            5 => [ 'bg' => 'Енергетика', 'en' => 'Energy'],
            6 => [ 'bg' => 'Защита на потребителите', 'en' => 'Consumer protection'],
            7 => [ 'bg' => 'Здравеопазване', 'en' => 'Healthcare'],
            8 => [ 'bg' => 'Земеделие и развитие на селските райони', 'en' => 'Agriculture and Rural Development'],
            9 => [ 'bg' => 'Качество и безопасност на храните', 'en' => 'Food quality and safety'],
            10 => [ 'bg' => 'Култура', 'en' => 'Culture'],
            11 => [ 'bg' => 'Младежка политика', 'en' => 'Youth Policy'],
            12 => [ 'bg' => 'Междусекторни политики', 'en' => 'Cross-cutting policies'],
            13 => [ 'bg' => 'Наука и технологии', 'en' => 'Science and Technology'],
            14 => [ 'bg' => 'Образование', 'en' => 'Education'],
            15 => [ 'bg' => 'Околна среда', 'en' => 'Education'],
            16 => [ 'bg' => 'Правосъдие и вътрешен ред', 'en' => 'Justice and internal order'],
            17 => [ 'bg' => 'Регионална политика', 'en' => 'Regional Policy'],
            18 => [ 'bg' => 'Социална политика и заетост', 'en' => 'Social policy and employment'],
            19 => [ 'bg' => 'Спорт', 'en' => 'Sport'],
            20 => [ 'bg' => 'Транспорт', 'en' => 'Transport'],
            21 => [ 'bg' => 'Туризъм', 'en' => 'Tourism'],
            22 => [ 'bg' => 'Финансова и данъчна политика', 'en' => 'Financial and Tax Policy'],
            23 => [ 'bg' => 'Електронно управление', 'en' => 'Electronic management'],
            24 => [ 'bg' => 'Икономика', 'en' => 'Economy'],
        ];

        foreach ($data as $id => $dbItem) {
            $item = Category::create([
                'id' => $id
            ]);

            if ($item) {
                foreach ($locales as $locale) {
                    $item->translateOrNew($locale['code'])->name = $dbItem[$locale['code']] ?? '';
                }
            }
            $item->save();
        }

        $tableToResetSeq = ['category'];
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
