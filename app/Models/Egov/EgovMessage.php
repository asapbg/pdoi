<?php

namespace App\Models\Egov;

use App\Models\ModelActivityExtend;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgovMessage extends ModelActivityExtend
{
    use SoftDeletes;
    protected $table = 'egov_message';
    public $timestamps = true;
    protected $guarded = [];

    const TYPE_REGISTER_DOCUMENT = 'MSG_DocumentRegistrationRequest';

    //activity
    protected string $logName = "egov_message";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->msg_guid;
    }
}
