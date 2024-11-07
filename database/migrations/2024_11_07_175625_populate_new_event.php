<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $locales = config('available_languages');

        //events
        $data = [
            14 => [
                'name' => [
                    'bg' => 'Регистрирано в процес на обработка',
                    'en' => 'Registered in processing',
                ],
                'next_events' => [1,4,2,5,Event::APP_EVENT_FINAL_DECISION],
                'id' => 14,
                'app_event' => 14,
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
                'mail_to_admin' => false,
                'mail_to_app' => false,
                'mail_to_new_admin' => false,
            ]
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
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
