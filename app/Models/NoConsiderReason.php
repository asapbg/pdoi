<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class NoConsiderReason extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.no_consider_reason';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'no_consider_reason';
    //activity
    protected string $logName = "no_consider_reason";

    protected $fillable = ['active'];

    public function scopeIsActive($query)
    {
        $query->where('no_consider_reason.active', 1);
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
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

    public static function optionsList()
    {
        return DB::table('no_consider_reason')
            ->select(['no_consider_reason.id', 'no_consider_reason_translations.name'])
            ->join('no_consider_reason_translations', 'no_consider_reason_translations.no_consider_reason_id', '=', 'no_consider_reason.id')
            ->where('no_consider_reason.active', '=', 1)
            ->where('no_consider_reason_translations.locale', '=', app()->getLocale())
            ->orderBy('no_consider_reason_translations.name', 'asc')
            ->get();
    }
}

