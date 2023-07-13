<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ProfileType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locales = config('available_languages');

        //profile types
        $types = [
            User::USER_TYPE_PERSON => ['Студент', 'Програмист', 'Журналист'],
            User::USER_TYPE_COMPANY => ['Търговско дружество', 'Медия' , 'Политическа партия'],
        ];

        foreach ($types as $legal_form => $row) {
            foreach ($row as $type) {
                $item = ProfileType::create([
                    'user_legal_form' => $legal_form,
                ]);

                if ($item) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->name = $type;
                    }
                }
                $item->save();
            }
        }
    }
}
