<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;

class CustomNotification extends DatabaseNotification
{
    const PAGINATE = 20;
    const INTERNAL_NOTIFICATION_TYPE = 'App\Notifications\CustomInternalNotification';

    public function scopeInternalCommunication($query){
        $query->where('type', '=', self::INTERNAL_NOTIFICATION_TYPE);
    }

    protected function internalSenderName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['sender_name'] ?? ''
        );
    }
}
