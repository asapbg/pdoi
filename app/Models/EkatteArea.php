<?php

namespace App\Models;


use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class EkatteArea extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['ime'];
    const MODULE_NAME = 'custom.area';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'ekatte_area';
    //activity
    protected string $logName = "area";

    protected $fillable = ['oblast', 'ekatte', 'region', 'document', 'abc', 'valid', 'active'];

    public function scopeIsActive($query)
    {
        $query->where('ekatte_area.active', 1);
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->ime;
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'ime' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:200']
            ],
        );
    }

    public static function optionsList()
    {
        return DB::table('ekatte_area')
            ->select(['ekatte_area.id', 'ekatte_area_translations.ime as name', 'ekatte_area.oblast as code'])
            ->join('ekatte_area_translations', 'ekatte_area_translations.ekatte_area_id', '=', 'ekatte_area.id')
            ->where('ekatte_area.active', '=', 1)
            ->where('ekatte_area_translations.locale', '=', app()->getLocale())
            ->orderBy('ekatte_area_translations.ime', 'asc')
            ->get();
    }
}
