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

    //communication statuses from old system
    const COMM_STATUS_WAIT_FOR_SEND = 1; //Чака изпращане
    const COMM_STATUS_SUCCESS_SEND = 2; //Успешно изпратено
    const COMM_STATUS_RECEIVED_FROM_OUTSIDE = 3; //Получено (отвън)
    const COMM_STATUS_NOT_IN_USE = 4; //(Не се използва)
    const COMM_STATUS_ERROR_BEFORE_SEND = 5; //Върната грешка преди изпращане
    const COMM_STATUS_ERROR_AFTER_SEND = 6; //Върната грешка след изпращане

    //activity
    protected string $logName = "egov_message";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->msg_guid;
    }
}
