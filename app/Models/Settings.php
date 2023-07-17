<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends ModelActivityExtend
{
    use SoftDeletes;
    const MODULE_NAME = 'custom.setting';
    protected $guarded = [];

    const SESSION_LIMIT_KEY = 'session_time_limit';

    public function scopeEditable($query)
    {
        $query->where('settings.editable', 1);
    }
}
