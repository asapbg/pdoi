<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Country extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.country';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'country';
    //activity
    protected string $logName = "country";

    protected $fillable = ['active', 'is_default'];

    public function scopeIsActive($query)
    {
        $query->where('country.active', 1);
    }

    public function scopeIsDefault($query)
    {
        $query->where('country.is_default', 1);
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
        return DB::table('country')
            ->select(['country.id', 'country_translations.name'])
            ->join('country_translations', 'country_translations.country_id', '=', 'country.id')
            ->where('country.active', '=', 1)
            ->where('country_translations.locale', '=', app()->getLocale())
            ->orderBy('country_translations.name', 'asc')
            ->get();
    }
}
