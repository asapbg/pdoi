<?php

namespace App\Models;

use App\Enums\ApplicationEventsEnum;
use App\Enums\CourtDecisionsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdoiApplicationEvent extends Model
{
    protected $table = 'pdoi_application_event';
    public $timestamps = true;

    protected function eventReasonName(): Attribute
    {
        $name = $this->event->name;
        switch ($this->event_type)
        {
            case ApplicationEventsEnum::FINAL_DECISION->value:
                if( $this->event_reason && $this->event_reason == PdoiApplicationStatusesEnum::NOT_APPROVED->value ) {
                    $name.= ' ('.__('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($this->event_reason)).($this->notApprovedReason ? ' - '.$this->notApprovedReason->name : '').')';
                } else{
                    if( !$this->event_reason ) {
//                        $name.= ' ('.__('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($this->application->status)).')';
                        $name.= ' ('.__('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue(PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value)).')';
                    } else{
                        $name.= ' ('.__('custom.application.status.'.PdoiApplicationStatusesEnum::keyByValue($this->event_reason)).')';
                    }
                }
                break;
            case ApplicationEventsEnum::EXTEND_TERM->value:
                if( $this->event_reason ) {
                    $name.= ' ('.$this->extendTimeReason->name.')';
                }
                break;
            case ApplicationEventsEnum::RENEW_PROCEDURE->value:
                if( $this->court_decision ) {
                    $name.= ' ('.__('custom.court_decision.'.CourtDecisionsEnum::keyByValue($this->court_decision)).')';
                }
                break;
            case ApplicationEventsEnum::FORWARD->value:
            case ApplicationEventsEnum::FORWARD_TO_SUB_SUBJECT->value:
                if( $this->new_resp_subject_id ) {
                    $name.= ' ('.$this->newSubject->subject_name.')';
                }
                break;
            case ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUBJECT->value:
            case ApplicationEventsEnum::FORWARD_TO_NOT_REGISTERED_SUB_SUBJECT->value:
                $name.= ' - '.$this->subject_name.'('.$this->subject_eik.')';
                break;
        }
        return Attribute::make(
            get: fn () => $name
        );
    }

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'id' , 'pdoi_application_id');
    }

    public function event(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Event::class, 'app_event' , 'event_type');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id' , 'user_reg');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')
            ->where('code_object', '=', File::CODE_OBJ_EVENT);
    }

    public function oldSubject(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'id' , 'old_resp_subject_id');
    }

    public function newSubject(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiResponseSubject::class, 'id' , 'new_resp_subject_id');
    }

    public function visibleFiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')
            ->where('visible_on_site', '=', 1)
            ->where('code_object', '=', File::CODE_OBJ_EVENT);
    }

    public function notApprovedReason(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReasonRefusal::class, 'id', 'reason_not_approved');
    }

    public function extendTimeReason(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ExtendTermsReason::class, 'id', 'event_reason');
    }

    public function noConsiderReason(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id' , 'no_consider_reason_id');
    }
}
