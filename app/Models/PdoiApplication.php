<?php

namespace App\Models;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Traits\FilterSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Self_;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class PdoiApplication extends ModelActivityExtend
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    const PAGINATE = 10;
    public $timestamps = true;
    const MODULE_NAME = 'custom.application';
    protected $table = 'pdoi_application';

    const UPLOAD_DIR = 'upload/';
    const MAX_FILE_SIZE = 10000; //10 mb in kilobyte
    const ALLOWED_FILE_EXTENSIONS = ['doc', 'docx', 'xsl', 'xslx', 'pdf', 'rtf', 'txt', 'gif', 'jpg', 'jpeg', 'png', 'zem', 'p7s'];

    //END TERMS PARAMETERS
    const DAYS_AFTER_SUBJECT_REGISTRATION = 14; //14 дни от регситрацията на зявлението при ЗС
    const CODE_OBJECT = 13; //TODO fix me I have no idea what it means

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    //activity
    protected string $logName = "applications";

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->application_uri;
    }

    protected function statusName(): Attribute
    {
        return Attribute::make(
            get: fn () => __('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($this->status))
        );
    }

    protected function fileFolder(): Attribute
    {
        return Attribute::make(
            get: fn () => self::UPLOAD_DIR.$this->id.'/'
        );
    }

    public function applicant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_reg');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function area(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteArea::class, 'id', 'area_id');
    }

    public function municipality(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteMunicipality::class, 'id', 'municipality_id');
    }

    public function settlement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteSettlement::class, 'id', 'settlement_id');
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'pdoi_application_category', 'pdoi_application_id', 'category_id');
    }

    public function responseSubject(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'id', 'response_subject_id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id');
    }
}
