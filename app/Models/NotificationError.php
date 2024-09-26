<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationError extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'notification_error';

    public function notification(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CustomNotification::class, 'id', 'notification_id');
    }
}
