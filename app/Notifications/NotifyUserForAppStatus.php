<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use App\Models\PdoiApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifyUserForAppStatus extends Notification
{
    use Queueable;

    private PdoiApplication $application;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
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
            'message' => 'Съобщение за промяна на статус на заявление до заявител',
            'subject' => __('mail.subject.new_application_status'),
            'files' => [],
            'type_channel' => $notifiable->delivery_method
        ];

        switch ($notifiable->delivery_method)
        {
            case DeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                break;
            default://email
                $communicationData['from_name'] = config('mail.from.name');
                $communicationData['from_email'] = config('mail.from.address');
                $communicationData['to_name'] = $notifiable->names;
                $communicationData['to_email'] = $notifiable->email;
        }
        return $communicationData;
    }
}
