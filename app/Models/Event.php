<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Event  extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.event';
    const DATE_TYPE_EVENT = 1;
    const DATE_TYPE_SUBJECT_REGISTRATION = 2;
    const EVENT_STATUS_COMPLETED = 1;
    const EVENT_STATUS_NOT_COMPLETED = 2;
    const APP_EVENT_FINAL_DECISION = 6;

    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'event';
    //activity
    protected string $logName = "event";

    protected $fillable = ['app_event', 'app_status', 'extend_terms_reason_id', 'days', 'date_type',
        'old_resp_subject', 'new_resp_subject', 'event_status', 'reason_not_approved', 'add_text',
        'files', 'event_delete', 'mail_to_admin', 'mail_to_app', 'mail_to_new_admin', 'court_decision'];

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

    public function scopeFinalDecision($query)
    {
        $query->where('app_event', '=', self::APP_EVENT_FINAL_DECISION);
    }

    public static function optionsList()
    {
        return DB::table('event')
            ->select(['event.id', 'event_translations.name'])
            ->join('event_translations', 'event_translations.event_id', '=', 'event.id')
            ->whereNull('event.deleted_at')
            ->where('event_translations.locale', '=', app()->getLocale())
            ->orderBy('event_translations.name', 'asc')
            ->get();
    }

    public function nextEvents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_next', 'event_id', 'event_app_event', 'app_event', 'id');
    }

    public function extendTimeReason(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ExtendTermsReason::class, 'id', 'extend_terms_reason_id');
    }
}
