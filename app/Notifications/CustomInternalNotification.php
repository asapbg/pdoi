<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomInternalNotification extends Notification
{
    use Queueable;

    private array $msgData;

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
        $channels = [];
        if ($this->msgData['internalMsg']) {
            $channels[] = 'database';
        }
        if ($this->msgData['mailMsg']) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject($this->msgData['subject'])
        ->markdown('emails.custom_msg', $this->msgData);
        return $message;
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
