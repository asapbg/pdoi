<?php

namespace App\Models\Egov;

use App\Models\ModelActivityExtend;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgovService extends ModelActivityExtend
{
    use SoftDeletes;
    protected $table = 'egov_service';
    public $timestamps = true;
    protected $guarded = [];

    //activity
    protected string $logName = "egov_service";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->service_name;
    }
}
