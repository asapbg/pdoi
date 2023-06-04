<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class ProfileType extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'profile_type';
    //activity
    protected string $logName = "profile_type";

    protected $fillable = ['user_legal_form', 'active'];

    public function scopeIsActive($query)
    {
        $query->where('profile_type.active', 1);
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

    public static function optionsList(int $legal_form = 0): \Illuminate\Support\Collection
    {
        return DB::table('profile_type')
            ->select(['profile_type.id', 'profile_type_translations.name'])
            ->join('profile_type_translations', 'profile_type_translations.profile_type_id', '=', 'profile_type.id')
            ->where('profile_type.active', '=', 1)
            ->where('profile_type.user_legal_form', '=', (int)$legal_form)
            ->where('profile_type_translations.locale', '=', app()->getLocale())
            ->orderBy('profile_type_translations.name', 'asc')
            ->get();
    }
}
