<?php

namespace Database\Seeders;

use App\Models\ChangeDecisionReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChangeDecisionReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Непубликувано решение',
            'Грешка в решението',
            'Необходимост от заличаване на лични данни',
        ];

        if(sizeof($data)){
            foreach ($data as $row){
                $item = new ChangeDecisionReason();
                $item->save();
                foreach (config('available_languages') as $k => $lang){
                    $item->translateOrNew($lang['code'])->name = $row;
                }
                $item->save();
            }
        }
    }
}
