<?php

namespace App\Models;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Traits\FilterSort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class PdoiApplication extends ModelActivityExtend implements Feedable
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    const PAGINATE = 10;
    public $timestamps = true;
    const MODULE_NAME = 'custom.applications';
    protected $table = 'pdoi_application';

    const UPLOAD_DIR = 'upload/';

    //END TERMS PARAMETERS
    const DAYS_AFTER_APPLY = 14; //14 дни от подаване на зявлението при ЗС
    const DAYS_AFTER_SUBJECT_REGISTRATION = 14; //14 дни от регситрацията на зявлението при ЗС
    const DAYS_AFTER_GIVE_INFORMATION = 14; //14 дни от потвърждението на предоставената информация

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    //activity
    protected string $logName = "applications";

    /**
     * @return FeedItem
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => $this->id,
            'title' => __('custom.application_system_title',
                [
                    'user' => ($this->names_publication ? $this->names : __('custom.anonymous_applicant') ),
                    'subject' => $this->responseSubject->subject_name,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'summary' => html_entity_decode($this->request).$this->statusName,
            'updated' => $this->updated_at,
            'link' => route('application.show', ['id' => $this->id]),
            'authorName' => $this->names_publication ? $this->names : __('custom.anonymous_applicant'),
            'authorEmail' => $this->email_publication ? $this->email : ''
        ]);
    }

    /**
     * We use tjis method for rss feed
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFeedItems(): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['responseSubject', 'responseSubject.translations'])
            ->where('updated_at', '>', Carbon::now()->startOfDay()->subDay())
            ->get();
    }

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
        //TODO fix me add parent clause to get application without subject id add clause in application policy too
        //if user has full permission skip else
        //filter list by user subject(rzs)
        $user = auth()->user();
        if( !$user->can('manage.*') ) {
            $query->where('response_subject_id', $user->administrative_unit);
        }
    }

    /**
     * !! Do not change
     * Using for cron
     * @param $query
     */
    public function scopeExpiredAndActive($query)
    {
        $query->where('pdoi_application.response_end_time', '<=', Carbon::now())
            ->whereIn('pdoi_application.status', PdoiApplicationStatusesEnum::notCompleted());
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'id', 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PdoiApplication::class, 'parent_id', 'id');
    }

    public function currentEvent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationEvent::class, 'pdoi_application_id','id')->latestOfMany();
    }

    public function lastFinalEvent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationEvent::class, 'pdoi_application_id','id')
            ->where('event_type', ApplicationEventsEnum::FINAL_DECISION->value)->latestOfMany();
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
}
