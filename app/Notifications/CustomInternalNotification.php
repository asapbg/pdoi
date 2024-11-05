<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
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
    public function __construct($msgData)
    {
        $this->msgData = $msgData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'sender_id' => $this->msgData['sender'] ??  null,
            'sender_name' => $this->msgData['sender_name'] ?? '',
            'subject' => $this->msgData['subject'],
            'message' => $this->msgData['msg']
        ];
    }
}
