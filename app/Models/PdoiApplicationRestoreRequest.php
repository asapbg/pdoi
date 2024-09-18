<?php

namespace App\Models;

use App\Traits\FilterSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class PdoiApplicationRestoreRequest extends ModelActivityExtend
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    //activity
    protected string $logName = "restore_requests";

    const PAGINATE = 20;
    const MODULE_NAME = ('custom.module_rzs_section');

    public $timestamps = true;
    protected $table = 'pdoi_application_restore_request';

    const STATUS_IN_PROCESS = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REGECTED = 2;


    public function getModelName() {
        return $this->application->application_uri;
    }

    protected function user_request(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !empty($value) ? html_entity_decode($value) : $value,
            set: fn (string|null $value) => !empty($value) ?  htmlentities(stripHtmlTags($value)) : $value,
        );
    }

    protected function reason_refuse(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !empty($value) ? html_entity_decode($value) : $value,
            set: fn (string|null $value) => !empty($value) ?  htmlentities(stripHtmlTags($value)) : $value,
        );
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id','applicant_id')->withTrashed();
    }

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'id','pdoi_application_id')->withTrashed();
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')->where('code_object', '=', File::CODE_OBJ_APPLICATION_RENEW);
    }

    protected function statusName(): Attribute
    {
        return Attribute::make(
            get: fn () => __('custom.restore_request.status.'.(int)$this->status)
        );
    }

    public function statusUser(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id','status_user_id')->withTrashed();
    }

}
