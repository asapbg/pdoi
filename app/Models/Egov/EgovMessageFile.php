<?php

namespace App\Models\Egov;

use App\Models\ModelActivityExtend;
use Illuminate\Database\Eloquent\SoftDeletes;

class EgovMessageFile extends ModelActivityExtend
{
    use SoftDeletes;
    protected $table = 'egov_message_file';
    public $timestamps = true;
    protected $guarded = [];

    //activity
    protected string $logName = "egov_message_file";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->filename;
    }
}
