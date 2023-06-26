<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CustomDbChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);
        $typeChannel = $data['type_channel'];
        unset($data['type_channel']);

        $rowData = [
            'id' => $notification->id,
            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
            //custom fields
            'type_channel' => $typeChannel
        ];
        return $notifiable->routeNotificationFor('database')->create($rowData);
    }

}
