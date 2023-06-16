<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class EkatteMunicipality extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['ime'];
    const MODULE_NAME = 'custom.municipality';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'ekatte_municipality';
    //activity
    protected string $logName = "municipality";

    protected $fillable = ['obstina', 'ekatte', 'category', 'document', 'abc', 'valid', 'active'];

    public function scopeIsActive($query)
    {
        $query->where('ekatte_municipality.active', 1);
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
        return DB::table('ekatte_municipality')
            ->select(['ekatte_municipality.id', 'ekatte_municipality_translations.ime as name' , 'ekatte_municipality.obstina as code'])
            ->join('ekatte_municipality_translations', 'ekatte_municipality_translations.ekatte_municipality_id', '=', 'ekatte_municipality.id')
            ->where('ekatte_municipality.active', '=', 1)
            ->where('ekatte_municipality_translations.locale', '=', app()->getLocale())
            ->orderBy('ekatte_municipality_translations.ime', 'asc')
            ->get();
    }
}
