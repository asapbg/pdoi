<?php

namespace App\Notifications;

use App\Enums\DeliveryMethodsEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomInternalNotification extends Notification
{
    use Queueable;

    private array $msgData;
    private string $chanel;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($msgData, $chanel)
    {
        $this->msgData = $msgData;
        $this->chanel = $chanel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        if ($this->chanel == 'internalMsg') {
            $channels[] = 'database';
        }
        if ($this->chanel == 'mailMsg') {
            $channels[] = CustomDbChannel::class;
        }
        return $channels;
    }

    public function toDatabase($notifiable)
    {
        $communicationData = [
            'sender_id' => $this->msgData['sender'] ? $this->msgData['sender']->id : null,
            'sender_name' => !empty($this->msgData['sender_name']) ? $this->msgData['sender_name'] : 'Системно съобщение',
            'message' => $this->msgData['msg'].'<p></p><p>'.(!empty($this->msgData['sender_name']) ? $this->msgData['sender_name'] : 'Системно съобщение').'</p>',
            'subject' => $this->msgData['subject'],
            'type_channel' => DeliveryMethodsEnum::EMAIL
        ];

        $communicationData['from_name'] = config('mail.from.name');
        $communicationData['from_email'] = config('mail.from.address');
        $communicationData['to_name'] = $notifiable->names;
        $communicationData['to_email'] = $notifiable->email;

        return $communicationData;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'sender_id' => $this->msgData['sender'] ? $this->msgData['sender']->id : null,
            'sender_name' => $this->msgData['sender'] ? $this->msgData['sender']->fullName() : 'Системно съобщение',
            'subject' => $this->msgData['subject'],
            'message' => $this->msgData['msg']
        ];
    }
}
