<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locales = config('available_languages');

        $data = array(
            [
                'slug' => 'help-appeal',
                'system_name' => Page::APPEAL_INFO_SYSTEM_PAGE,
                'name' => 'Процедура по обжалване на решение',
                'content' => '<ul>
                                    <li>Всеки заявител за достъп до обществена информация, може да обжалва решението за предоставяне/отказ на информация съгласно Чл. 40. (1) (Изм. - ДВ, бр. 30 от 2006 г., в сила от 12.07.2006 г., изм. - ДВ, бр. 49 от 2007 г., изм. - ДВ, бр. 77 от 2018 г., в сила от 01.01.2019 г.) на Закона за достъп до обществен информация (ЗДОИ)</li>
                                    <li>Съгласно чл.40 (2)  (Изм. - ДВ, бр. 30 от 2006 г., в сила от 12.07.2006 г., изм. - ДВ, бр. 39 от 2011 г., изм. - ДВ, бр. 77 от 2018 г., в сила от 01.01.2019 г.)  Решенията за предоставяне на достъп до обществена информация или за отказ за предоставяне на достъп до обществена информация на субектите по чл. 3, ал. 2 се обжалват пред съответния административен съд по реда на Административнопроцесуалния кодекс.</li>
                                    <li>Съгласно чл.40 (3) (Нова - ДВ, бр. 77 от 2018 г., в сила от 01.01.2019 г.) Решението на административния съд не подлежи на касационно оспорване.</li>
                                </ul>
                                <h5>Помощна информация:</h5>
                                <ul>
                                    <li>Компетентният съд, пред който трябва да подадете жалбата си е административният съд, чието седалище съвпада с местоседалището на решаващия орган.</li>
                                    <li>За да бъде разгледана Вашата жалба от компетентния съд, моля попълнете всички полета от шаблонния формуляр.</li>
                                    <li>Съгласно чл.150 ал.2 от АПК във Вашата жалба задъжително трябва да посочите доказателствата, които иска да бъдат събрани, и да представите писмените доказателства, с които разполага.</li>
                                    <li>Съгласно чл.150 ал.3 от АПК жалбата не може да съдържа нецензурни думи, обиди или заплахи.</li>
                                </ul>'
            ],
            [
                'slug' => 'contact',
                'system_name' => Page::CONTACT_SYSTEM_PAGE,
                'name' => 'Контакти',
                'content' => '<h4>Администратори на платформата</h4><p>Петя Цанкова - p.tsankova@government.bg</p><p>Поддръжка - pmo@asap.bg</p>'
            ],
            [
                'slug' => 'video-instruction',
                'system_name' => Page::VIDEO_INSTRUCTION_PAGE,
                'name' => 'Видео инструкции',
                'content' => ''
            ],
            [
                'slug' => 'user-manual',
                'system_name' => Page::USER_MANUAL_PAGE,
                'name' => 'Потребителско ръководство',
                'content' => 'Потребителско ръководство'
            ],
            [
                'slug' => 'faq',
                'system_name' => Page::FAQ_PAGE,
                'name' => 'Често задавани въпроси',
                'content' => 'Често задавани въпроси'
            ],
        );

        foreach ($data as $page) {
            $dbPage = Page::where('slug', '=', $page['slug'])->first();
            if( $dbPage ) {
//                foreach ($locales as $locale) {
//                    $dbPage->translateOrNew($locale['code'])->name = $page['name'];
//                    $dbPage->translateOrNew($locale['code'])->content = $page['content'];
//                }
//                $dbPage->save();
                $this->command->info("Page with slug ".$page['slug']." updated successfully");
            } else{
                $item = Page::create([
                    'slug' => $page['slug'],
                    'system_name' => $page['system_name']
                ]);

                if( $item ) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->name = $page['name'];
                        $item->translateOrNew($locale['code'])->content = $page['content'];
                    }
                    $item->save();
                    $this->command->info("Page with slug ".$page['slug']." created successfully");
                }
            }
        }
    }
}
