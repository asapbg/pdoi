<?php

namespace App\Models;


use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class EkatteArea extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['ime'];
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
}
