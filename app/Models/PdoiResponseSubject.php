<?php

namespace App\Models;

use App\Models\Egov\EgovOrganisation;
use App\Traits\FilterSort;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class PdoiResponseSubject extends ModelActivityExtend implements TranslatableContract
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity, Translatable, Notifiable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['subject_name', 'address', 'add_info', 'court_text'];
    public $timestamps = true;
    const MODULE_NAME = 'custom.rzs_items';

    protected $table = 'pdoi_response_subject';
    //activity
    protected string $logName = "subjects";

    protected $fillable = ['eik', 'region', 'municipality', 'town', 'phone', 'fax', 'email', 'date_from'
        , 'date_to', 'adm_register', 'redirect_only', 'adm_level', 'parent_id', 'zip_code', 'nomer_register'
        , 'active', 'court_id', 'delivery_method', 'batch_id', 'type'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public function scopeIsActive($query)
    {
        $query->where('pdoi_response_subject.active', 1);
    }

    public function scopeCanApplyTo($query)
    {
        $query->where('pdoi_response_subject.redirect_only', 0)
            ->where('pdoi_response_subject.active', 1);
    }

    public function scopeIsManual($query)
    {
        $query->where('pdoi_response_subject.adm_register', 0);
    }

    public function scopeFilterByRole($query)
    {
        $user = auth()->user();
        if($user && !$user->hasAnyRole([CustomRole::SUPER_USER_ROLE, CustomRole::ADMIN_USER_ROLE])){
            $ids = [0];
            if($user->responseSubject){
                $ids = self::getAdmStructureIds($user->responseSubject->adm_level);
            }
            $query->whereIn('pdoi_response_subject.adm_level', $ids)
                ->where(function ($q) use ($user){
                    $q->where('pdoi_response_subject.adm_level', '<>', $user->responseSubject->adm_level)
                        ->orWhere('pdoi_response_subject.id', '=', $user->responseSubject->id);
                    });
        }
    }

    public static function getAdmStructureIds($parent, $currentArray = array()){
        $ids = sizeof($currentArray) ? array_merge($currentArray, [$parent]) : [$parent];
        $children = RzsSection::where('parent_id', '=', $parent)->get();
        if($children->count()){
            foreach ($children as $child){
                $ids = self::getAdmStructureIds($child->id, $ids);
            }
        }
        return $ids;
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RzsSection::class, 'adm_level', 'adm_level');
    }

    public function court(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'id', 'court_id');
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PdoiApplication::class, 'id', 'response_subject_id');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'administrative_unit', 'id');
    }

    public function activeUsers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'administrative_unit', 'id')->where('active', '=', 1);
    }

    public function egovOrganisation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EgovOrganisation::class, 'eik', 'eik');
    }

    public function regionObj(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteArea::class, 'id', 'region');
    }

    public function municipalityObj(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteMunicipality::class, 'id', 'municipality');
    }

    public function townObj(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EkatteSettlement::class, 'id', 'town');
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'subject_name' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
            'address' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
            'add_info' => [
                'type' => 'textarea',
                'rules' => ['nullable', 'string', 'max:500']
            ],
            'court_text' => [
                'type' => 'text',
                'rules' => ['nullable', 'required_without:court', 'string', 'max:255']
            ]
        );
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }

    public static function simpleOptionsList(): \Illuminate\Support\Collection
    {
        return DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.subject_name as name'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
            ->orderBy('pdoi_response_subject_translations.subject_name', 'asc')
            ->get();
    }

    /**
     * @param array|int $ignoreId
     * @return \Illuminate\Support\Collection
     */
    public static function optionsList(array|int $ignoreId = [], $filterByRole = false, $ignoreOnlyRedirect = false, $withDeliveryMethod = false): \Illuminate\Support\Collection
    {
        $user = auth()->user();
        $ids = null;
        if($filterByRole){
            if($user->responseSubject){
                $ids = self::getAdmStructureIds($user->responseSubject->adm_level);
            }
        }

        $query = DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.subject_name as name', 'pdoi_response_subject.adm_level as parent'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->whereNull('deleted_at')
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
            ->when($ids, function ($query) use($ids, $user) {
                return $query->whereIn('pdoi_response_subject.adm_level', $ids)->where(function ($q) use ($user){
                    $q->where('pdoi_response_subject.adm_level', '<>', $user->responseSubject->adm_level)
                        ->orWhere('pdoi_response_subject.id', '=', $user->responseSubject->id);
                });
            })
            ->when($ignoreOnlyRedirect, function ($query) {
                return $query->where('pdoi_response_subject.redirect_only', '=', 0);
            })->when($withDeliveryMethod, function ($query) {
                return $query->where('pdoi_response_subject.delivery_method', '>', 0);
            })
            ->orderBy('pdoi_response_subject.id', 'asc')
            ->orderBy('pdoi_response_subject.adm_level', 'asc')
            ->orderBy('pdoi_response_subject_translations.subject_name', 'asc');

        if( !empty($ignoreId) ) {
            if( is_array($ignoreId) ) {
                $query->whereNotIn('pdoi_response_subject.id', $ignoreId);
            } else {
                $query->where('pdoi_response_subject.id', '<>', $ignoreId);
            }
        }
        return $query->get();
    }

    /**
     * We use this to draw subjects tree template in modals and pages
     * @return array
     */
    public static function getTree($filter = [])
    {
        $tree = [];
        $subjects = DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id'
                , 'pdoi_response_subject_translations.subject_name as name'
                , 'pdoi_response_subject.adm_level as section_parent'
                , 'pdoi_response_subject.parent_id as subject_parent'
//                , DB::raw('case when pdoi_response_subject.parent_id is null then pdoi_response_subject.adm_level else pdoi_response_subject.parent_id end as parent')
                , DB::raw('1 as selectable')])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->whereNull('pdoi_response_subject.deleted_at')
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale());

        $ignoreRedirectOnly = $filter['ignore_redirect_only'] ?? 0;
        if( $ignoreRedirectOnly ) {
            $subjects->where('pdoi_response_subject.redirect_only', '=', 0);
        }
        $onlyWithDelivery = $filter['only_with_delivery'] ?? 0;
        if( $onlyWithDelivery ) {
            $subjects->where('pdoi_response_subject.delivery_method', '>', 0);
        }


        $allSubjectsAndSections = DB::table("rzs_section")
            ->select(['rzs_section.adm_level as id'
                , 'rzs_section_translations.name'
                , 'rzs_section.parent_id as section_parent'
                , DB::raw('null as subject_parent')
                , DB::raw('0 as selectable')])
            ->join('rzs_section_translations', 'rzs_section_translations.rzs_section_id', '=', 'rzs_section.id')
            ->where('rzs_section.active', '=', 1)
            ->whereNull('rzs_section.deleted_at')
            ->where('rzs_section_translations.locale', '=', app()->getLocale())
            ->union($subjects)->orderBy('name','asc')
            ->get();
        if( $allSubjectsAndSections->count() ) {
            foreach ($allSubjectsAndSections as $subject) {
                if( !$subject->selectable && !$subject->section_parent ) {
                    $tree[] = array(
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'selectable' => $subject->selectable,
                        'parent' => null,
                        'children' => self::subjectChildren($subject->id, !$subject->selectable, $allSubjectsAndSections)
                    );
                }
            }
        }
        return $tree;
    }

    private static function subjectChildren(int $parent, int $parentIsSection, $subjects): array
    {
        $children = [];
        if( $subjects->count() ) {
            foreach ($subjects as $subject) {
                $isSubjectChild = !$parentIsSection && (int)$subject->subject_parent == $parent;
                $isSectionChild = $parentIsSection &&
                    (
                        (!$subject->selectable && (int)$subject->section_parent == $parent)
                        || ($subject->selectable && (int)$subject->section_parent == $parent && is_null($subject->subject_parent))
                    );
                if($isSectionChild || $isSubjectChild) {
                    $children[] = array(
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'selectable' => $subject->selectable,
                        'parent' => $subject->subject_parent ?? $subject->section_parent,
                        'children' => self::subjectChildren($subject->id, !$subject->selectable, $subjects)
                    );
                }
            }
        }
        return $children;
    }

    public static function statisticSubjectsWithAdmin(): array
    {
        return DB::select('
            select
                max(pdoi_response_subject_translations.subject_name) as rzs_name,
                count(users.id) as rzs_administrators
            from pdoi_response_subject
            join pdoi_response_subject_translations
                on pdoi_response_subject_translations.pdoi_response_subject_id = pdoi_response_subject.id and pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'
            left join users on pdoi_response_subject.id = users.administrative_unit
            where
                (
                    users.id is null
                    or (
                        users.deleted_at is null
                        and users.active = 1
                    )
                )
                and pdoi_response_subject.deleted_at is null
                and pdoi_response_subject.active = 1
            group by pdoi_response_subject.id
            order by max(pdoi_response_subject_translations.subject_name);
        ');
    }

    public static function mostAskedSubjects($limit = 0): array
    {
        return DB::select('
            select A.*
            from (
                select
                    max(pdoi_response_subject_translations.subject_name) as rzs_name,
                    count(pdoi_application.id) as applications
                from pdoi_response_subject
                join pdoi_response_subject_translations
                    on pdoi_response_subject_translations.pdoi_response_subject_id = pdoi_response_subject.id and pdoi_response_subject_translations.locale = \''.app()->getLocale().'\'
                join pdoi_application on pdoi_application.response_subject_id = pdoi_response_subject.id
                group by pdoi_response_subject.id
            ) as A
            order by A.applications desc
            '.($limit ? 'limit '.$limit : '').';
        ');
    }

    public static function isChildOf($parentId, $childId): bool
    {
        $check = DB::select('
            with recursive rec as (
                select
                    id, parent_id
                from pdoi_response_subject
                where
                    parent_id = '.(int)$parentId.'
                union all
                    select
                        c.id, c.parent_id
                    from pdoi_response_subject c
                    join rec p on c.parent_id = p.id
            ) select id from rec where rec.id = '.(int)$childId.';
        ');
        return (boolean)sizeof($check);
    }

    /**
     * Return all emails of users connected with current subject with changes
     * @return array
     */
    public function getAlertUsersEmail(): array
    {
        $emails = [];
        $users = User::IsActive()->where(function ($q){
            $q->where(function ($q){
                $q->where('administrative_unit', $this->id)->role('admin_moderator');
            })->orWhere(function ($q){
                $q->role('admin');
            });
        })->get();

        if( $users ) {
            $emails = $users->pluck('email')->toArray();
        }
        return $emails;
    }

    /**
     * Return all emails of users connected with current subject with changes
     * @return array
     */
    public function getModeratorsEmail(): array
    {
        $emails = [];
        $users = User::IsActive()->where(function ($q){
            $q->where(function ($q){
                $q->where('administrative_unit', $this->id)->role('admin_moderator');
            });
        })->get();

        if( $users ) {
            $emails = $users->pluck('email')->toArray();
        }
        return $emails;
    }

    /**
     * Using to get actual moderators at the moment of apply
     * @return mixed
     */
    public function getModerators()
    {
        return User::IsActive()->where(function ($q){
            $q->where(function ($q){
                $q->where('administrative_unit', $this->id)->role('admin_moderator');
            });
        })->get();
    }

    /**
     * Return all emails of users connected with current subject
     * @return array
     */
    public function getConnectedUsersEmails(): array
    {
        $emails = [];
        $users = User::IsActive()->where(function ($q){
            $q->where(function ($q){
                $q->where('administrative_unit', $this->id);
            });
        })->get();

        if( $users ) {
            $emails = $users->pluck('email')->toArray();
        }
        return $emails;
    }

}
