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
            'summary' => html_entity_decode($this->request).PHP_EOL.__('custom.status').': '.$this->statusName,
            'updated' => $this->updated_at ?? $this->created_at,
            'link' => route('application.show', ['id' => $this->id]),
            'authorName' => $this->names_publication ? $this->full_names : __('custom.anonymous_applicant'),
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
            ->orderByRaw("(case when updated_at is null then created_at else updated_at end) desc")
            ->limit(env('RSS_ITEMS', 20))
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

    protected function nonRegisteredSubjectName(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->response_subject_id ? $this->not_registered_subject_name.'('.$this->not_registered_subject_eik.')' : null
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

    public function userFiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')->where('code_object', '=', File::CODE_OBJ_APPLICATION)->whereNotNull('user_reg');
    }

    public function profileType(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProfileType::class, 'id', 'profile_type');
    }

    public static function statisticRenewed($filter, $export = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $fromDate = isset($filter['fromDate']) && !empty($filter['fromDate']) ? $filter['fromDate'] : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = isset($filter['toDate']) && !empty($filter['toDate']) ? $filter['toDate'] : Carbon::now()->endOfMonth()->endOfDay();
        $subject = (isset($filter['subject']) && !empty($filter['subject'])) ? (int)$filter['subject'] : null;

        $query = DB::table('pdoi_application')
            ->join('pdoi_application_event', 'pdoi_application_event.pdoi_application_id', '=', 'pdoi_application.id')
            ->join('pdoi_response_subject', 'pdoi_response_subject.id', '=', 'pdoi_application.response_subject_id')
            ->join('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')->whereRaw('pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'');
            })
            ->select(DB::raw('max(pdoi_response_subject_translations.subject_name) as subject_name'), DB::raw('count(distinct(pdoi_application.id)) as applications_cnt'));

        $query->when($subject, function ($q, $subject) {
            return $q->where('pdoi_application.response_subject_id', $subject);
        })->when($fromDate, function ($q, $fromDate) {
            return $q->where('pdoi_application.created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        })->when($toDate, function ($q, $toDate) {
            return $q->where('pdoi_application.created_at', '<=', Carbon::parse($toDate)->endOfDay());
        })
            ->where('pdoi_application_event.event_type', '=', ApplicationEventsEnum::RENEW_PROCEDURE->value);

        if( $export ) {
            return $query->groupBy('pdoi_response_subject.id')
                ->orderBy('pdoi_response_subject.id')->get();
        }
        return $query->groupBy('pdoi_response_subject.id')
            ->orderBy('pdoi_response_subject.id')
            ->paginate(20);
    }

    public static function statisticTerms($filter, $export = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $fromDate = isset($filter['fromDate']) && !empty($filter['fromDate']) ? $filter['fromDate'] : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = isset($filter['toDate']) && !empty($filter['toDate']) ? $filter['toDate'] : Carbon::now()->endOfMonth()->endOfDay();
        $subject = (isset($filter['subject']) && !empty($filter['subject'])) ? (int)$filter['subject'] : null;

        $query = DB::table('pdoi_application')
            ->join('pdoi_response_subject', 'pdoi_response_subject.id', '=', 'pdoi_application.response_subject_id')
            ->join('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')->whereRaw('pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'');
            })
            ->select(
                DB::raw('max(pdoi_response_subject_translations.subject_name) as subject_name'),
                DB::raw('count(pdoi_application.id) as total_applications'),
                DB::raw('
                    sum(case when
                        pdoi_application.response_end_time is not null
                        and pdoi_application.status_date is not null
                        and pdoi_application.status_date <= pdoi_application.response_end_time
                        and pdoi_application.status in ('.PdoiApplicationStatusesEnum::APPROVED->value.','.PdoiApplicationStatusesEnum::PART_APPROVED->value.','.PdoiApplicationStatusesEnum::NOT_APPROVED->value.','.PdoiApplicationStatusesEnum::INFO_NOT_EXIST->value.','.PdoiApplicationStatusesEnum::FORWARDED->value.')
                       then 1 else 0 end) as in_time_applications
                '),
                DB::raw('sum(case when pdoi_application.status = '.PdoiApplicationStatusesEnum::NO_REVIEW->value.' then 1 else 0 end) as expired_applications')
            );

        $query->when($subject, function ($q, $subject) {
            return $q->where('pdoi_application.response_subject_id', $subject);
        })->when($fromDate, function ($q, $fromDate) {
            return $q->where('pdoi_application.created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        })->when($toDate, function ($q, $toDate) {
            return $q->where('pdoi_application.created_at', '<=', Carbon::parse($toDate)->endOfDay());
        });

        if( $export ) {
            return $query->groupBy('pdoi_application.response_subject_id')
                ->orderBy('pdoi_application.response_subject_id')->get();
        }
        return $query->groupBy('pdoi_application.response_subject_id')
            ->orderBy('pdoi_application.response_subject_id')
            ->paginate(20);
    }

    public static function statisticForwarded($filter, $export = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $fromDate = isset($filter['formDate']) && !empty($filter['formDate']) ? $filter['formDate'] : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = isset($filter['toDate']) && !empty($filter['toDate']) ? $filter['toDate'] : Carbon::now()->endOfMonth()->endOfDay();
        $subject = (isset($filter['subject']) && !empty($filter['subject'])) ? (int)$filter['subject'] : null;

        $query = DB::table('pdoi_application')
            ->join('pdoi_application_event', 'pdoi_application_event.pdoi_application_id', '=', 'pdoi_application.id')
            ->join('pdoi_response_subject', 'pdoi_response_subject.id', '=', 'pdoi_application.response_subject_id')
            ->join('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')->whereRaw('pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'');
            })
            ->select(DB::raw('max(pdoi_response_subject_translations.subject_name) as subject_name'), DB::raw('count(distinct(pdoi_application.id)) as applications_cnt'));

        $query->when($subject, function ($q, $subject) {
            return $q->where('pdoi_application.response_subject_id', $subject);
        })->when($fromDate, function ($q, $fromDate) {
            return $q->where('pdoi_application.created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        })->when($toDate, function ($q, $toDate) {
            return $q->where('pdoi_application.created_at', '<=', Carbon::parse($toDate)->endOfDay());
        })
            ->whereNotNull('pdoi_application_event.old_resp_subject_id')
            //->whereNotNull('pdoi_application_event.new_resp_subject_id')
            ->whereColumn('pdoi_application_event.old_resp_subject_id', '=', 'pdoi_application.response_subject_id');

        if( $export ) {
            return $query->groupBy('pdoi_response_subject.id')
                ->orderBy('pdoi_response_subject.id')->get();
        }

        return $query->groupBy('pdoi_response_subject.id')
            ->orderBy('pdoi_response_subject.id')
            ->paginate(20);
    }

    public static function statisticGroupBy($filter, $export = 0): \Illuminate\Contracts\Pagination\LengthAwarePaginator|array|\Illuminate\Support\Collection
    {
        $page = isset($filter['page']) && !empty($filter['page']) ? (int)$filter['page'] : 1;
        $typeQuery = isset($filter['groupBy']) && !empty($filter['groupBy']) ? $filter['groupBy'] : 'subject';
        $fromDate = isset($filter['fromDate']) && !empty($filter['fromDate']) ? $filter['fromDate'] : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = isset($filter['toDate']) && !empty($filter['toDate']) ? $filter['toDate'] : Carbon::now()->endOfMonth()->endOfDay();
        $subject = (isset($filter['subject']) && !empty($filter['subject'])) ? (int)$filter['subject'] : null;
        $status = (isset($filter['status']) && !empty($filter['status'])) ? (int)$filter['status'] : null;
        $category = (isset($filter['category']) && !empty($filter['category'])) ? (int)$filter['category'] : null;

        switch ($typeQuery)
        {
            case 'subject':
                $name = 'max(pdoi_response_subject_translations.subject_name)';
                $value = 'count(pdoi_application.id)';
                $groupBy = 'pdoi_application.response_subject_id';
                $orderBy = 'pdoi_application.response_subject_id';
                break;
            case 'applicant_type':
                $name = 'case when pdoi_application.applicant_type = '.User::USER_TYPE_PERSON.' then \''.__('custom.users.legal_form.'.User::USER_TYPE_PERSON).'\' else \''.__('custom.users.legal_form.'.User::USER_TYPE_COMPANY).'\' end';
                $value = 'count(pdoi_application.id)';
                $groupBy = 'pdoi_application.applicant_type';
                $orderBy = 'pdoi_application.applicant_type';
                break;
            case 'profile_type':
                $name = 'max(coalesce(profile_type_translations.name, \'NA\'))';
                $value = 'count(pdoi_application.id)';
                $groupBy = 'pdoi_application.profile_type';
                $orderBy = 'pdoi_application.profile_type';
                break;
            case 'status':
                $name = 'pdoi_application.status';
                $value = 'count(pdoi_application.id)';
                $groupBy = 'pdoi_application.status';
                $orderBy = 'pdoi_application.status';
                break;
            case 'country':
                $name = 'coalesce(country_translations.name, \'NA\')';
                $value = 'count(pdoi_application.id)';
                $groupBy = ['country.id', 'country_translations.name'];
                $orderBy = 'country_translations.name';
                break;
            case 'category':
                $name = 'coalesce(category_translations.name, \'NA\')';
                $value = 'count(pdoi_application.id)';
                $groupBy = ['category.id', 'category_translations.name'];
                $orderBy = 'category_translations.name';
                break;
            default:
                $name = null;
                $value = null;
        }

        if( !$name || !$value || !$groupBy ) {
            return [];
        }

        $query = DB::table('pdoi_application')
            ->join('pdoi_response_subject', 'pdoi_response_subject.id', '=', 'pdoi_application.response_subject_id')
            ->join('pdoi_response_subject_translations', function ($join){
                $join->on('pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')->whereRaw('pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'');
            })
            ->leftJoin('profile_type', 'profile_type.id', '=' ,'pdoi_application.profile_type')
            ->leftJoin('profile_type_translations', function ($join){
                $join->on('profile_type_translations.profile_type_id', '=', 'profile_type.id')->whereRaw('pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'');
            })
            ->leftJoin('country', 'pdoi_application.country_id', '=', 'country.id')
            ->leftJoin('country_translations', function ($join){
                $join->on('country_translations.country_id', '=', 'country.id')->whereRaw('country_translations.locale = \''.app()->getLocale().'\'');
            })
            ->leftJoin('pdoi_application_category', 'pdoi_application_category.pdoi_application_id', '=', 'pdoi_application.id')
            ->leftJoin('category', 'category.id', '=', 'pdoi_application_category.category_id')
            ->leftJoin('category_translations', function ($join){
                $join->on('category_translations.category_id', '=', 'category.id')->whereRaw('category_translations.locale = \''.app()->getLocale().'\'');
            })
            ->select(DB::raw($name.' as name'), DB::raw($value.' as value_cnt'));

        $query->when($category, function ($q, $category) {
            return $q->where('category.id', $category);
        })->when($status, function ($q, $status) {
                return $q->where('pdoi_application.status', $status);
        })->when($subject, function ($q, $subject) {
                return $q->where('pdoi_application.response_subject_id', $subject);
        })->when($fromDate, function ($q, $fromDate) {
                return $q->where('pdoi_application.created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        })->when($toDate, function ($q, $toDate) {
                return $q->where('pdoi_application.created_at', '<=', Carbon::parse($toDate)->endOfDay());
        });

        if( $export ) {
            return $query->groupBy($groupBy)
                ->orderBy($orderBy)->get();
        }
        return $query->groupBy($groupBy)
            ->orderBy($orderBy)
            ->paginate(20);
    }
}
