<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdoiApplicationEvent extends Model
{
    protected $table = 'pdoi_application_event';
    public $timestamps = true;

    public function event(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Event::class, 'app_event' , 'event_type');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id' , 'user_reg');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')->where('code_object', '=', File::CODE_OBJ_EVENT);
    }
}
