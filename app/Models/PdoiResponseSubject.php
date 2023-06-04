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

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['subject_name', 'address', 'add_info'];
    public $timestamps = true;

    protected $table = 'pdoi_response_subject';
    //activity
    protected string $logName = "subjects";

    protected $fillable = ['eik', 'region', 'municipality', 'town', 'phone', 'fax', 'email', 'date_from'
        , 'date_to', 'adm_register', 'redirect_only', 'adm_level', 'parent_id', 'zip_code', 'nomer_register', 'active'];
    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public function scopeIsActive($query)
    {
        $query->where('pdoi_response_subject.active', 1);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'parent_id', 'id');
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RzsSection::class, 'adm_level', 'adm_level');
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
                'type' => 'textarea',
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

    /**
     * @param array|int $ignoreId
     * @return \Illuminate\Support\Collection
     */
    public static function optionsList(array|int $ignoreId = []): \Illuminate\Support\Collection
    {
        $query = DB::table('pdoi_response_subject')
            ->select(['pdoi_response_subject.id', 'pdoi_response_subject_translations.subject_name as name'])
            ->join('pdoi_response_subject_translations', 'pdoi_response_subject_translations.pdoi_response_subject_id', '=', 'pdoi_response_subject.id')
            ->where('pdoi_response_subject.active', '=', 1)
            ->whereNull('deleted_at')
            ->where('pdoi_response_subject_translations.locale', '=', app()->getLocale())
            ->orderBy('pdoi_response_subject.id', 'asc')
            ->orderBy('pdoi_response_subject.adm_level', 'asc')
            ->orderBy('pdoi_response_subject_translations.subject_name', 'asc');

        if( !empty($ignoreId) ) {
            if( is_array($ignoreId) ) {
                $query->whereNotIn('pdoi_response_subject.id', $ignoreId);
            } else {
                $query->where('pdoi_response_subject.id', '<>', $ignoreId);
            }
        }
        return $query->get();
    }

}
