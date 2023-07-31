<?php

namespace App\Models\Egov;

use App\Models\ModelActivityExtend;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgovMessageCoresp extends ModelActivityExtend
{
    use SoftDeletes;
    protected $table = 'egov_message_coresp';
    public $timestamps = true;
    protected $guarded = [];

    //activity
    protected string $logName = "egov_message_coresp";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }
}
