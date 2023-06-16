<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class EkatteSettlement extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['ime'];
    const MODULE_NAME = 'custom.settlement';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'ekatte_settlement';
    //activity
    protected string $logName = "settlement";

    protected $fillable = ['ekatte', 'tvm', 'oblast', 'obstina', 'kmetstvo', 'kind', 'category'
        , 'altitude', 'document', 'tsb', 'abc', 'valid'];


    public function scopeIsActive($query)
    {
        $query->where('ekatte_settlement.active', 1);
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
        return DB::table('ekatte_settlement')
            ->select(['ekatte_settlement.id', 'ekatte_settlement_translations.ime as name', 'ekatte_settlement.oblast as area', 'ekatte_settlement.obstina as municipality'])
            ->join('ekatte_settlement_translations', 'ekatte_settlement_translations.ekatte_settlement_id', '=', 'ekatte_settlement.id')
            ->where('ekatte_settlement.active', '=', 1)
            ->where('ekatte_settlement_translations.locale', '=', app()->getLocale())
            ->orderBy('ekatte_settlement_translations.ime', 'asc')
            ->get();
    }
}
