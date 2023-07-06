<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_next')->truncate();
        DB::table('event_translations')->truncate();
        DB::table('event')->truncate();

        $locales = config('available_languages');

        //events
        $data = [
            1 => [
                'name' => [
                    'bg' => 'Препращане по компетентност',
                    'en' => 'Application Forwarding',
                ],
                'next_events' => [1],
                'id' => 1,
                'app_event' => 4,
                'app_status' => 9,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => true,
                'new_resp_subject' => true,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => false,
                'mail_to_admin' => false,
                'mail_to_app' => true,
                'mail_to_new_admin' => true,
            ],
            2 => [
                'name' => [
                    'bg' => 'Искане на допълнителна информация',
                    'en' => 'Request of an additional information',
                ],
                'next_events' => [6],
                'id' => 2,
                'app_event' => 2,
                'app_status' => 3,
                'extend_terms_reason_id' => null,
                'days' => 30,
                'date_type' => 1,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 2,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => true,
                'mail_to_admin' => false,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            3 => [
                'name' => [
                    'bg' => 'Предоставяне на допълнителна информация',
                    'en' => 'Supply of an additional information',
                ],
                'next_events' => [1,4,2,5,6],
                'id' => 3,
                'app_event' => 3,
                'app_status' => 3,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => false,
                'mail_to_admin' => true,
                'mail_to_app' => false,
                'mail_to_new_admin' => false,
            ],
            4 => [
                'name' => [
                    'bg' => 'Удължаване на срока',
                    'en' => 'Deadline extension',
                ],
                'next_events' => [6],
                'id' => 4,
                'app_event' => 5,
                'app_status' => 3,
                'extend_terms_reason_id' => 1,
                'days' => 14,
                'date_type' => 2,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => true,
                'mail_to_admin' => false,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            5 => [
                'name' => [
                    'bg' => 'Удължаване на срока',
                    'en' => 'Deadline extension',
                ],
                'next_events' => [6],
                'id' => 5,
                'app_event' => 5,
                'app_status' => 3,
                'extend_terms_reason_id' => 2,
                'days' => 10,
                'date_type' => 2,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => true,
                'mail_to_admin' => false,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            6 => [
                'name' => [
                    'bg' => 'Крайно решение',
                    'en' => 'Final decision',
                ],
                'next_events' => [],
                'id' => 6,
                'app_event' => 6,
                'app_status' => null,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => 10013,
                'court_decision' => false,
                'add_text' => true,
                'files' => true,
                'event_delete' => true,
                'mail_to_admin' => false,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            7 => [
                'name' => [
                    'bg' => 'Изпратено към деловодна система',
                    'en' => 'Sent to document management system',
                ],
                'next_events' => [8],
                'id' => 7,
                'app_event' => 7,
                'app_status' => 2,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => false,
                'files' => false,
                'event_delete' => false,
                'mail_to_admin' => false,
                'mail_to_app' => false,
                'mail_to_new_admin' => false,
            ],
            8 => [
                'name' => [
                    'bg' => 'Потвърждение от деловодна система',
                    'en' => 'Confirmation from the document management system',
                ],
                'next_events' => [1,4,2,5,6],
                'id' => 8,
                'app_event' => 8,
                'app_status' => 3,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => false,
                'files' => false,
                'event_delete' => false,
                'mail_to_admin' => true,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            9 => [
                'name' => [
                    'bg' => 'Подаване на заявление',
                    'en' => 'Submitting an application',
                ],
                'next_events' => [1,2,4,5,6],
                'id' => 9,
                'app_event' => 9,
                'app_status' => 1,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => false,
                'add_text' => false,
                'files' => false,
                'event_delete' => false,
                'mail_to_admin' => true,
                'mail_to_app' => true,
                'mail_to_new_admin' => false,
            ],
            10 => [
                'name' => [
                    'bg' => 'Възобновяване на процедура',
                    'en' => 'Resumption of procedure',
                ],
                'next_events' => [6],
                'id' => 10,
                'app_event' => 10,
                'app_status' => 10,
                'extend_terms_reason_id' => null,
                'days' => null,
                'date_type' => null,
                'old_resp_subject' => false,
                'new_resp_subject' => false,
                'event_status' => 1,
                'reason_not_approved' => null,
                'court_decision' => true,
                'add_text' => true,
                'files' => true,
                'event_delete' => false,
                'mail_to_admin' => false,
                'mail_to_app' => false,
                'mail_to_new_admin' => false,
            ],
        ];

        foreach ($data as $row) {
            $names = $row['name'];
            $nextEvents = $row['next_events'];
            unset($row['name'], $row['next_events']);
            $exist = Event::find((int)$row['id']);
            if( !$exist ) {
                $item = Event::create($row);
                if ($item) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->name = $names[$locale['code']];
                    }

                    $item->nextEvents()->sync($nextEvents);
                }
                $item->save();
                $this->command->info("Event with name ".$names['bg']." created successfully");
            }
        }

        $tableToResetSeq = ['event', 'event_translations'];
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
