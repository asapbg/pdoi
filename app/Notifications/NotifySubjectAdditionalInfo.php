<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Models\Egov\EgovMessage;
use App\Models\Egov\EgovOrganisation;
use App\Models\PdoiApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Selective\XmlDSig\PrivateKeyStore;
use Selective\XmlDSig\Algorithm;
use Selective\XmlDSig\CryptoSigner;
use Selective\XmlDSig\XmlSigner;

class NotifySubjectAdditionalInfo extends Notification
{
    use Queueable;

    private PdoiApplication $application;
    private int|null $egov_message_id;
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
        $messageContent = 'Предоставена е допълнителна информация по заявление №'.$this->application->application_uri.':';
        $messageContent .= $this->application->lastEvent->add_text;
        $communicationData = [
            'message' => $messageContent,
            'subject' => __('mail.subject.register_new_application'),
            'application_id' => $this->application->id,
            'files' => [],
            'type_channel' => $notifiable->delivery_method != PdoiSubjectDeliveryMethodsEnum::SEOS->value ? $notifiable->delivery_method : PdoiSubjectDeliveryMethodsEnum::EMAIL->value
        ];
        switch ($notifiable->delivery_method)
        {
            case PdoiSubjectDeliveryMethodsEnum::SDES->value: //система за сигурно електронно връчване
                $eDeliveryConfig = config('e_delivery');
                if( env('APP_ENV') != 'production' ) {
                    $communicationData['ssev_profile_id'] = config('e_delivery.local_ssev_profile_id');
                } else {
                    $communicationData['to_group'] = $eDeliveryConfig['group_ids']['egov'];
                    $communicationData['to_identity'] = $notifiable->eik;
                    $communicationData['ssev_profile_id'] = $notifiable->ssev_profile_id ?? 0;
                }
                break;
            default://email
                $communicationData['from_name'] = config('mail.from.name');
                $communicationData['from_email'] = config('mail.from.address');
                $communicationData['to_name'] = $notifiable->names;
                $communicationData['to_email'] = $this->application->responseSubject->email;
                $communicationData['files']= [];
        }
        return $communicationData;
    }
}
