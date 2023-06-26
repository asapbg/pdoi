<?php

namespace App\Notifications;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\PdoiApplication;
use App\Services\ApplicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifySubjectNewApplication extends Notification
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
            'message' => 'Съобщение за ново заявление до задължен субект през системата за сигурно втъчване',
            'subject' => __('mail.subject.register_new_application'),
            'application_id' => $this->application->id,
            'files' => [],
            'type_channel' => $notifiable->delivery_method
        ];
        switch ($notifiable->delivery_method)
        {
            case PdoiSubjectDeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                break;
            case PdoiSubjectDeliveryMethodsEnum::RKS->value: //деловодна система
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
