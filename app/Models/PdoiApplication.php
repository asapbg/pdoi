<?php

namespace App\Models;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Traits\FilterSort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class PdoiApplication extends ModelActivityExtend
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    const PAGINATE = 10;
    public $timestamps = true;
    const MODULE_NAME = 'custom.applications';
    protected $table = 'pdoi_application';

    const UPLOAD_DIR = 'upload/';

    //END TERMS PARAMETERS
    const DAYS_AFTER_SUBJECT_REGISTRATION = 14; //14 дни от регситрацията на зявлението при ЗС

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

    protected function statusStyle(): Attribute
    {
        $class = 'light';
        if(in_array($this->status, [PdoiApplicationStatusesEnum::RECEIVED->value, PdoiApplicationStatusesEnum::REGISTRATION_TO_SUBJECT->value, PdoiApplicationStatusesEnum::IN_PROCESS->value])) {
            $class = $this->response_end_time > Carbon::now() ? 'success' : 'warning';
        } else {
            if( $this->status === PdoiApplicationStatusesEnum::NOT_APPROVED->value ) {
                $class = 'danger';
            } elseif ( $this->status === PdoiApplicationStatusesEnum::FORWARDED->value ) {
                $class = 'info';
            }
        }

        return Attribute::make(
            get: fn () => $class
        );
    }

    protected function fileFolder(): Attribute
    {
        return Attribute::make(
            get: fn () => self::UPLOAD_DIR.$this->id.'/'
        );
    }

    public function scopeByUserSubjects($query)
    {
        //if user has full permission skip else
        //filter list by user subject(rzs)
        $user = auth()->user();
        if( !$user->can('manage.*') ) {
            $query->where('response_subject_id', $user->administrative_unit);
        }
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'parent_id', 'id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PdoiApplication::class, 'parent_id', 'id');
    }

    public function currentEvent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationEvent::class, 'pdoi_application_id','id')->latestOfMany();
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PdoiApplicationEvent::class, 'pdoi_application_id', 'id');
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
        return $this->hasMany(File::class, 'id_object', 'id')->where('code_object', '=', File::CODE_OBJ_APPLICATION);
    }

    public function profileType(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProfileType::class, 'id', 'profile_type');
    }

    public static function optionsList(): \Illuminate\Support\Collection
    {
        return DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.subject_name as name'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
            ->orderBy('pdoi_response_subject_translations.subject_name', 'asc')
            ->get();
    }
}
