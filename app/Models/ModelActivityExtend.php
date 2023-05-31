<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as ModelAlias;
use Spatie\Activitylog\LogOptions;

class ModelActivityExtend extends ModelAlias
{
    /**
     * Log user activity
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->logName);
    }
}
