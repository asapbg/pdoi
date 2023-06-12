<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class Category extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.categories';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'category';
    //activity
    protected string $logName = "category";

    protected $fillable = ['active'];

    public function scopeIsActive($query)
    {
        $query->where('category.active', 1);
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
        return DB::table('category')
            ->select(['category.id', 'category_translations.name'])
            ->join('category_translations', 'category_translations.category_id', '=', 'category.id')
            ->where('category.active', '=', 1)
            ->where('category_translations.locale', '=', app()->getLocale())
            ->orderBy('category_translations.name', 'asc')
            ->get();
    }
}
