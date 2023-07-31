<?php

namespace App\Models\Egov;

use App\Models\ModelActivityExtend;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgovOrganisation  extends ModelActivityExtend
{
    use SoftDeletes;
    protected $table = 'egov_organisation';
    public $timestamps = true;
    protected $guarded = [];

    //activity
    protected string $logName = "egov_organisation";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->administrative_body_name;
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EgovService::class, 'id', 'id_org')->where('status', '=', 1);
    }
}
