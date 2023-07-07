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

    public static function optionsList()
    {
        return DB::table('rzs_section')
            ->select(['rzs_section.id', 'rzs_section_translations.name'])
            ->join('rzs_section_translations', 'rzs_section_translations.rzs_section_id', '=', 'rzs_section.id')
            ->where('rzs_section.active', '=', 1)
            ->where('rzs_section_translations.locale', '=', app()->getLocale())
            ->orderBy('rzs_section_translations.name', 'asc')
            ->get();
    }
}
