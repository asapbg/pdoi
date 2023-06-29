<?php

namespace App\Models;

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
        , 'active', 'court_id', 'delivery_method'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public function scopeIsActive($query)
    {
        $query->where('pdoi_response_subject.active', 1);
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RzsSection::class, 'adm_level', 'adm_level');
    }

    public function court(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'id', 'court_id');
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

    /**
     * @param array|int $ignoreId
     * @return \Illuminate\Support\Collection
     */
    public static function optionsList(array|int $ignoreId = []): \Illuminate\Support\Collection
    {
        $query = DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.subject_name as name', 'pdoi_response_subject.adm_level as parent'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->whereNull('deleted_at')
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
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
    public static function getTree($filter)
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

        if( isset($filter['redirect_only']) ) {
            $subjects->where('pdoi_response_subject.redirect_only', '=', (int)$filter['redirect_only']);
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

}
