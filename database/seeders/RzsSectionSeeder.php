<?php

namespace Database\Seeders;

use App\Models\RzsSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RzsSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rzs_section_translations')->truncate();
        DB::table('rzs_section')->truncate();
        DB::table('pdoi_response_subject_translations')->truncate();
        DB::table('pdoi_response_subject')->truncate();

        $locales = config('available_languages');

        //sections
        $data = [
            1 => ['Административни структури, създадени с постановление на МС', 'Act60AdiministrativeStructure'],
            2 => ['Административни структури, създадени със закон', 'ExecutivePowerAdministrativeStructure'],
            3 => ['Администрации на държавни комисии', 'StateCommisionAdministrion'],
            4 => ['Държавни Агенции', 'StateAgency'],
            5 => ['Изпълнителни агенции', 'ExecutiveAgency'],
            6 => ['Администрация на Министерския съвет', 'CouncilOfMinistersAdministration'],
            7 => ['Министерства', 'Ministry'],
            8 => ['Областни администрации', 'RegionalAdminisration'],
            9 => ['Общински администрации', 'MunicipalAdministration'],
            10 => ['Специализирани териториални администрации, създадени като юридически лица с нормативен акт', 'SpecializedLocalAdministration'],
            11 => ['Районни администрации', 'AreaMunicipalAdministration'],
            12 => ['ДРУГИ', 'NA'],
            13 => ['Съвети', 'Council'],
            14 => ['Държавно-обществени консултативни комисии', 'StatePublicConsultativeCommission']
        ];

        foreach ($data as $code => $rzsSection) {
            $item = RzsSection::create([
                'adm_level' => $code,
                'system_name' => $rzsSection[1]
            ]);
            if ($item) {
                foreach ($locales as $locale) {
                    $item->translateOrNew($locale['code'])->name = $rzsSection[0];
                }
            }
            $item->save();
        }

        //subjects
        $csvFile = fopen(base_path("database/import_files/rzs_subjects.csv"), "r");
        while (($data = fgetcsv($csvFile, 2000, "|")) !== FALSE) {
            if(is_array($data) && sizeof($data) == 21) {
                $item = \App\Models\PdoiResponseSubject::create([
                    'id' => $data[0],
                    'eik' => $data[1],
                    'region' => !empty($data[3]) ? $data[3] : null,
                    'municipality' => !empty($data[4]) ? $data[4] : null,
                    'town' => !empty($data[5]) ? $data[5] : null,
                    'phone' => $data[7],
                    'fax' => $data[8],
                    'email' => $data[9],
                    'date_from' => $data[11],
                    'date_to' => empty($data[12]) ? null : $data[12],
                    'adm_register' => (int)!($data[13] == 't'),
                    'adm_level' => (int)$data[14] > 0 ? $data[14] : null,
                    'zip_code' => (int)$data[19] > 0 ? $data[19] : null,
                    'nomer_register' => $data[20],
                    'active' => !empty($data[12]) ? !(Carbon::now()->startOfDay()->gte($data[12])) : 1
                ]);

                if ($item) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->subject_name = $data[2];
                        $item->translateOrNew($locale['code'])->address = $data[6];
                        $item->translateOrNew($locale['code'])->add_info = $data[10];
                    }
                }
                $item->save();
            }
        }

        fclose($csvFile);
    }
}
