<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use App\Models\PdoiApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifyUserNeedMoreInfo extends Notification
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
            'message' => 'Съобщение за нужда от повече информация',
            'subject' => __('mail.subject.need_more_nfo'),
            'files' => [],
            'type_channel' => $notifiable->delivery_method
        ];

        switch ($notifiable->delivery_method)
        {
            case DeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                $eDeliveryConfig = config('e_delivery');
                if( env('APP_ENV') != 'production' ) {
                    $communicationData['ssev_profile_id'] = env('LOCAL_TO_SSEV_PROFILE_ID');
                } else {
                    $communicationData['to_group'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $eDeliveryConfig['group_ids']['person'] : $eDeliveryConfig['group_ids']['company'];
                    $communicationData['to_identity'] = $notifiable->legal_form == User::USER_TYPE_PERSON ? $notifiable-> person_identity : $notifiable->company_identity;
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
