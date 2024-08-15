<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class ChangeDecisionReason extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.change_decision_reason';
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'change_decision_reason';
    //activity
    protected string $logName = "change_decision_reason";

    protected $fillable = ['active'];

    public function scopeIsActive($query)
    {
        $query->where('change_decision_reason.active', 1);
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
        return DB::table('change_decision_reason')
            ->select(['change_decision_reason.id', 'change_decision_reason_translations.name'])
            ->join('change_decision_reason_translations', 'change_decision_reason_translations.change_decision_reason_id', '=', 'change_decision_reason.id')
            ->where('change_decision_reason.active', '=', 1)
            ->where('change_decision_reason_translations.locale', '=', app()->getLocale())
            ->orderBy('change_decision_reason_translations.name', 'asc')
            ->get();
    }
}

