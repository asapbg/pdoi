<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use App\Models\PdoiApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifyUserExtendTerm extends Notification
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
            'message' => 'Съобщение за удължаване на срок и причини за това',
            'subject' => __('mail.subject.extend_term_and_reason'),
            'files' => [],
            'type_channel' => $notifiable->delivery_method,
            'application_id' => $this->application->id
        ];

        switch ($notifiable->delivery_method)
        {
            case DeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                $eDeliveryConfig = config('e_delivery');
                if( env('APP_ENV') != 'production' ) {
                    $communicationData['ssev_profile_id'] = config('e_delivery.local_ssev_profile_id');
                } else {
                    $communicationData['to_group'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $eDeliveryConfig['group_ids']['person'] : $eDeliveryConfig['group_ids']['company'];
                    $communicationData['to_identity'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $notifiable->person_identity : $notifiable->company_identity;
                    $communicationData['ssev_profile_id'] = $notifiable->ssev_profile_id ?? 0;
                }
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
