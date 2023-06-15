<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomRole extends Role
{
    use LogsActivity;
    use SoftDeletes;

    protected string $logName = "roles";

    const MODULE_NAME = 'custom.roles';
    const PAGINATE = 20;

    const SUPER_USER_ROLE = 'service_user';
    const FULL_ACCESS_RULE = 'manage.*';
    const WEB_ACCESS_RULE = ['manage.*', 'pdoi.*', 'pdoi.web'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the name of the role
     */
    public function getModelName() {
        return $this->display_name;
    }

    /**
     * Log Sector's activity
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
