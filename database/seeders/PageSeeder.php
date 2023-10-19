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
                'content' => '<div class="row">
        <div class="col-md-6 p-3">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Регистрация</button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Accordion Item #2</button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Accordion Item #3</button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 p-3">
            <div class="accordion" id="accordionExamples">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOnes">
                        <button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOnes" aria-expanded="true" aria-controls="collapseOnes">Вход</button>
                    </h2>
                    <div id="collapseOnes" class="accordion-collapse collapse show" aria-labelledby="headingOnes" data-bs-parent="#accordionExamples" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwos">
                        <button class="accordion-button py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwos" aria-expanded="false" aria-controls="collapseTwos">Accordion Item #2</button>
                    </h2>
                    <div id="collapseTwos" class="accordion-collapse collapse" aria-labelledby="headingTwos" data-bs-parent="#accordionExamples" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThrees">
                        <button class="accordion-button py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThrees" aria-expanded="false" aria-controls="collapseThrees">Accordion Item #3</button>
                    </h2>
                    <div id="collapseThrees" class="accordion-collapse collapse" aria-labelledby="headingThrees" data-bs-parent="#accordionExamples" style="">
                        <div class="accordion-body">
                            <div class="video-container" style="position: relative;overflow: hidden;width: 100%;padding-top: 56.25%;">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/NF-zDMfmgY8?si=LFQlmPjAQy35CWqC" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen="" style="position: absolute;top: 0;left: 0;bottom: 0;right: 0;width: 100%;height: 100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>'
            ],
            [
                'slug' => 'user-manual',
                'system_name' => Page::USER_MANUAL_PAGE,
                'name' => 'Потребителско ръководство',
                'content' => 'Потребителско ръководство'
            ],
        );

        foreach ($data as $page) {
            $dbPage = Page::where('slug', '=', $page['slug'])->first();
            if( $dbPage ) {
                foreach ($locales as $locale) {
                    $dbPage->translateOrNew($locale['code'])->name = $page['name'];
                    $dbPage->translateOrNew($locale['code'])->content = $page['content'];
                }
                $dbPage->save();
                $this->command->info("Page with slug ".$page['slug']." updated successfully");
                continue;
            }

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
