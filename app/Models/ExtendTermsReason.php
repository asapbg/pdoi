<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExtendTermsReason extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.nomenclature.extend_terms_reason';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'extend_terms_reason';
    //activity
    protected string $logName = "extend_terms_reason";

    protected $fillable = ['active'];

    public function scopeIsActive($query)
    {
        $query->where('extend_terms_reason.active', 1);
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
        return DB::table('extend_terms_reason')
            ->select(['extend_terms_reason.id', 'extend_terms_reason_translations.name'])
            ->join('extend_terms_reason_translations', 'extend_terms_reason_translations.category_id', '=', 'extend_terms_reason.id')
            ->where('extend_terms_reason.active', '=', 1)
            ->where('extend_terms_reason_translations.locale', '=', app()->getLocale())
            ->orderBy('extend_terms_reason_translations.name', 'asc')
            ->get();
    }
}
