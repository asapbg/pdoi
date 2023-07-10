<?php

namespace Database\Seeders;

use App\Enums\MailTemplateTypesEnum;
use App\Models\MailTemplates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mail_template')->truncate();

        $templates = [
            1 => [
                'name' => 'Шаблон автоматично препращане към ЗС',
                'type' => MailTemplateTypesEnum::RZS_AUTO_FORWARD->value,
                'content' => '<p>УВАЖАЕМИ .......,</p><p>В :administration е постъпило заявление с вх. № :reg_number от :date_apply г. от :applicant за предоставяне на информация по Закона за достъп до обществена информация (ЗДОИ).</p><p>След като заявлението беше разгледано по чл. 28 от ЗДОИ, от фактическа страна беше установено следното:</p><p>Съгласно чл. 3, ал. 1 от ЗДОИ законът се прилага по отношение на обществена информация, която е създадена или съхранявана от държавен орган.</p><p>Когато органът не разполага с исканата информация, но има данни за нейното местонахождение, съгласно чл. 32 от ЗДОИ, той препраща съответно заявление, като уведомява за това заявителя.</p>'
            ],
            2 => [
                'name' => 'Шаблон препращане по компетентност към ЗС',
                'type' => MailTemplateTypesEnum::RZS_MANUAL_FORWARD->value,
                'content' => '<p>УВАЖАЕМИ .......,</p><p>В :administration е постъпило заявление с вх. № :reg_number от :date_apply г. от :applicant за предоставяне на информация по Закона за достъп до обществена информация (ЗДОИ).</p><p>След като заявлението беше разгледано по чл. 28 от ЗДОИ, от фактическа страна беше установено следното:</p><p>Съгласно чл. 3, ал. 1 от ЗДОИ законът се прилага по отношение на обществена информация, която е създадена или съхранявана от държавен орган.</p><p>Когато органът не разполага с исканата информация, но има данни за нейното местонахождение, съгласно чл. 32 от ЗДОИ, той препраща съответно заявление, като уведомява за това заявителя.</p><p>На основание чл. 32 от ЗДОИ, препращам по компетентност заявление с вх. № :reg_number от :forward_date_apply г. за достъп до обществена информация по ЗДОИ до :forward_administration (задължен субект), за отговор до заявителя.</p>'
            ]
        ];

        foreach ($templates as $id => $row) {
            $item = MailTemplates::create([
                'name' => $row['name'],
                'type' => $row['type'],
                'content' => $row['content']
            ]);
            $item->save();
        }

        \Illuminate\Support\Facades\DB::statement(
            "do $$
                        declare newId int;
                        begin
                            select (max(id) +1)  from mail_template into newId;
                            execute 'alter SEQUENCE mail_template_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
        );
    }
}
