<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use App\Models\PdoiApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class NotifyUserForAppStatus extends Notification
{
    use Queueable;

    private PdoiApplication $application;
    private $editedEvent;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
        $this->editedEvent = $application->lastFinalEvent ? $application->finalEvents()->where('id', '<>', $application->lastFinalEvent->id)->first() : null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDbChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        $communicationData = [
            'message' => 'Здравейте '.$this->application->names.','.PHP_EOL.(
                $this->editedEvent ?
                    'Извършена е промяна след обявяване на крайното решение, по подадено от вас заявление в '.__('custom.full_app_name').':'
                    : 'Информация за статуса на подадено от вас заявление в '.__('custom.full_app_name').':'
                ).PHP_EOL.
                ($this->editedEvent ? 'Причина за промяна на крайното решение: '.strip_tags(html_entity_decode($this->editedEvent->edit_final_decision_reason)).PHP_EOL : '').
                'Рег. №: '.$this->application->application_uri.';'.PHP_EOL.
                'Задължен субект: '.$this->application->responseSubject->subject_name.';'.PHP_EOL.
                'Статус: '.__('custom.application.status.'.\App\Enums\PdoiApplicationStatusesEnum::keyByValue($this->application->status)).';'.PHP_EOL.
                'Дата на промяна: '.displayDate($this->application->status_date).';'.PHP_EOL.
                'Срок за отговор: '.displayDate($this->application->response_end_time).';'.PHP_EOL.
                '*Забележка: Това съобщение е генерирано автоматично - моля, не му отговаряйте.',
            'subject' => __('mail.subject.new_application_status'),
            'files' => [],
            'type_channel' => $notifiable->delivery_method,
            'application_id' => $this->application->id
        ];

        switch ($notifiable->delivery_method)
        {
            case DeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                $eDeliveryConfig = config('e_delivery');
                if( config('app.env') != 'production' ) {
                    $communicationData['ssev_profile_id'] = config('e_delivery.local_ssev_profile_id');
                } else {
                    $communicationData['to_group'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $eDeliveryConfig['group_ids']['person'] : $eDeliveryConfig['group_ids']['company'];
                    $communicationData['to_identity'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $notifiable->person_identity : $notifiable->company_identity;
                    $communicationData['ssev_profile_id'] = $notifiable->ssev_profile_id ?? 0;
                }

                if( $this->application->files->count() ) {
                    foreach ($this->application->files as $f) {
                        $communicationData['files'][] = [
                            'id' => $f->id,
                            'name' => $f->filename,
                            'content_type' => $f->content_type,
                            'binary_content' => base64_encode(Storage::disk('local')->get($f->path))
                        ];
                    }
                }
                break;
            default://email
                $communicationData['from_name'] = config('mail.from.name');
                $communicationData['from_email'] = config('mail.from.address');
                $communicationData['to_name'] = $notifiable->names;
                $communicationData['to_email'] = $notifiable->email;
                $communicationData['files']= $this->application->files ? $this->application->files->pluck('id')->toArray() : [];
        }
        return $communicationData;
    }
}
