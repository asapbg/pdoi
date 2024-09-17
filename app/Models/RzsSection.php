<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class RzsSection  extends ModelActivityExtend implements TranslatableContract
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = ('custom.module_rzs_section');
    public $timestamps = true;

    protected $table = 'rzs_section';
    //activity
    protected string $logName = "rzs_sections";

    protected $fillable = ['adm_level', 'system_name', 'parent_id', 'active', 'manual'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public function scopeIsActive($query)
    {
        $query->where('rzs_section.active', 1);
    }

    public function scopeFilterByRole($query)
    {
        $user = auth()->user();
        if($user && !$user->hasAnyRole([CustomRole::SUPER_USER_ROLE, CustomRole::ADMIN_USER_ROLE])){
            $ids = [0];
            if($user->responseSubject){
                $ids = self::getAdmStructureIds($user->responseSubject->adm_level);
            }
            $query->whereIn('id', $ids);
        }
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RzsSection::class, 'adm_level', 'parent_id');
    }

    public function subjects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PdoiResponseSubject::class, 'adm_level', 'adm_level');
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'name' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
        );
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }

    public static function optionsList($excludeIds = array(0), $filterByRole = false): \Illuminate\Support\Collection
    {
        $ids = null;
        if($filterByRole){
            $user = auth()->user();
            if($user->responseSubject){
                $ids = self::getAdmStructureIds($user->responseSubject->adm_level);
            }
        }


        return DB::table('rzs_section')
            ->select(['rzs_section.id', 'rzs_section_translations.name'])
            ->join('rzs_section_translations', 'rzs_section_translations.rzs_section_id', '=', 'rzs_section.id')
            ->where('rzs_section.active', '=', 1)
            ->where('rzs_section_translations.locale', '=', app()->getLocale())
            ->when($ids, function ($query) use($ids) {
                return $query->whereIn('rzs_section.id', $ids);
            })
            ->whereNotIn('rzs_section.id', $excludeIds)
            ->orderBy('rzs_section_translations.name', 'asc')
            ->get();
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
}
