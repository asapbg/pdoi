<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class PdoiResponseSubject extends ModelActivityExtend implements TranslatableContract
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity, Translatable;

    const TRANSLATABLE_FIELDS = ['subject_name', 'address', 'add_info'];
    public $timestamps = true;

    protected $table = 'pdoi_response_subject';
    //activity
    protected string $logName = "subjects";

    protected $fillable = ['eik', 'region', 'municipality', 'town', 'phone', 'fax', 'email', 'date_from', 'date_to', 'adm_register', 'adm_level', 'zip_code', 'nomer_register', 'active'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public function scopeIsActive($query)
    {
        $query->where('pdoi_response_subject.active', 1);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'adm_level', 'id');
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'subject_name' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
            'address' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
            'add_info' => [
                'type' => 'text',
                'rules' => ['nullable', 'string', 'max:500']
            ],
        );
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }

    public static function optionsList()
    {
        return DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.name'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->whereNull('deleted_at')
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
            ->orderBy('pdoi_response_subject.id', 'asc')
            ->orderBy('pdoi_response_subject.parent_id', 'asc')
            ->orderBy('pdoi_response_subject_translations.name', 'asc');
    }

}
