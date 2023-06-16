<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class ReasonRefusal extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.reason_refusals';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'reason_refusal';
    //activity
    protected string $logName = "reason_refusal";

    protected $fillable = ['active'];

    public function scopeIsActive($query)
    {
        $query->where('reason_refusal.active', 1);
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
        return DB::table('reason_refusal')
            ->select(['reason_refusal.id', 'reason_refusal_translations.name'])
            ->join('reason_refusal_translations', 'reason_refusal_translations.reason_refusal_id', '=', 'reason_refusal.id')
            ->where('reason_refusal.active', '=', 1)
            ->where('reason_refusal_translations.locale', '=', app()->getLocale())
            ->orderBy('reason_refusal_translations.name', 'asc')
            ->get();
    }
}
