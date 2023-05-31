<?php

namespace App\Models;

use App\Traits\FilterSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class EgovOrganisation extends ModelActivityExtend
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'egov_organisation';
    //activity
    protected string $logName = "subjects";


    public function scopeIsActive($query)
    {
        $query->where('egov_organisation.active', self::STATUS_ACTIVE);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EgovOrganisation::class, 'parent_guid', 'id');
    }

}
