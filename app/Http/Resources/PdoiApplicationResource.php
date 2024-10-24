<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PdoiApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uri' => $this->application_uri,
            'title' => __('custom.application_system_title',
                [
                    'user' => ($this->names_publication ? $this->names : __('custom.users.legal_form.'.$this->applicant_type) ),
                    'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'my_title' => __('custom.own_application_system_title',
                [
                    'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'renew_title' => __('custom.own_application_renew_title',
                [
                    'reg_num' => $this->application_uri,
                ]),
            'response_subject_name' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
            'response' =>$this->lastFinalEvent ? ($this->lastFinalEvent->event_type == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value && $this->lastFinalEvent->event_reason == \App\Enums\PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value && !$this->lastFinalEvent->noConsiderReason ? null : $this->response) : $this->response,
            'response_is_changed_message' => $this->finalEvents->count() > 1,
            'response_date' => displayDate($this->response_date),
            'no_consider_reason_name' => $this->lastFinalEvent ? ($this->lastFinalEvent->noConsiderReason ? $this->lastFinalEvent->noConsiderReason->name : null) : null,
            'no_consider_reason_text' => $this->lastFinalEvent ? ($this->lastFinalEvent->event_type == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value && $this->lastFinalEvent->event_reason == \App\Enums\PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value && !$this->lastFinalEvent->noConsiderReason ? $this->lastFinalEvent->add_text : null) : null,
            'request' => $this->request,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'statusStyle' => $this->statusStyle,
            'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
            'term' => $this->response_end_time,
            'names' => $this->full_names,
            'public_names' => $this->names_publication,
            'phone' => $this->phone,
            'public_phone' => $this->phone_publication,
            'email' => $this->email,
            'public_email' => $this->email_publication,
            'public_address' => $this->address_publication,
            'country' => $this->country->name,
            'area' => $this->area ? $this->area->ime : '',
            'municipality' => $this->municipality ? $this->municipality->ime : '',
            'settlement' => $this->settlement ? $this->settlement->ime : '',
            'post_code' => $this->post_code,
            'address' => $this->address,
            'address_second' => $this->address_second,
            'files' => (new FileCollection($this->files))->resolve(),
            'final_files' => $this->lastFinalEvent && $this->lastFinalEvent->visibleFiles->count() ? (new FileCollection($this->lastFinalEvent->visibleFiles))->resolve() : [],
            'themes' => $this->categories->count() ?
                $this->categories->map(function ($item) {
                    return $item->name;
                })->toArray() : [],
            'events' => $this->events->count() ?
                $this->events->map(function ($item) {
                    return [
                        'name' => $item->eventReasonName,
                        'date' => displayDateTime($item->created_at),
                        'user_name' => auth()->user() && $item->user ?
                            (auth()->user()->id == $item->user_reg ? 'Аз'
                                : ($item->user->user_type == User::USER_TYPE_INTERNAL ? $item->user->names
                                    : ($this->names_publication ? $this->names : __('custom.users.legal_form.'.$this->applicant_type) ) )
                            )
                            : '',
                        'user_type' => auth()->user() ?
                            ($item->user_reg > 0 ?
                                ($item->user->user_type == User::USER_TYPE_EXTERNAL ? __('custom.applicant') : __('custom.admin') )
                                : 'Системен')
                            : '',
                        'old_subject' => $item->old_resp_subject_id ? $item->oldSubject->subject_name : null,
                        'new_subject' => $item->new_resp_subject_id ? $item->newSubject->subject_name : null,
                        'court_decision' => $item->court_decision ? __('custom.court_decision.'.\App\Enums\CourtDecisionsEnum::keyByValue((int)$item->court_decision)) : null,
                        'end_date' => !is_null($item->event_end_date) ? displayDate($item->event_end_date) : null,
                        'no_consider_reason_name' => $item->noConsiderReason ? $item->noConsiderReason->name : null,
                        'no_consider_reason_text' => $item->event_type == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value && $item->event_reason == \App\Enums\PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value && !$item->noConsiderReason ? $item->add_text : null,
                        'text' => !empty($item->edit_final_decision_reason) ? null : ($item->event_type == \App\Enums\ApplicationEventsEnum::FINAL_DECISION->value && $item->event_reason == \App\Enums\PdoiApplicationStatusesEnum::NO_CONSIDER_REASON->value ? ($item->noConsiderReason ? $item->add_text : null) : $item->add_text),
                        'edit_reason' => $item->edit_final_decision_reason,
                        'files' => empty($item->edit_final_decision_reason) ? (new FileCollection($item->files))->resolve() : [],
                    ];
                })->toArray() : [],
            'cnt_visits' => (int)$this->number_of_visits,
            'children' => $this->children->count() ? $this->children->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reg_num' => $item->application_uri,
                    'subject' => $item->response_subject_id ? $item->responseSubject->subject_name : $item->nonRegisteredSubjectName,
                    'date' => displayDateTime($item->created_at),
                    'status' => __('custom.application.status.'. \App\Enums\PdoiApplicationStatusesEnum::keyByValue($item->status))
                ];
            })->toArray() : [],
            'renewRequests' => $this->restoreRequests->count() ? $this->restoreRequests->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->statusName,
                    'status_date' => $item->status_datetime,
                    'reason_refuse' => $item->reason_refuse,
                    'statusUser' => $item->statusUser ? $item->statusUser->fullName() : null,
                    'created_at' => $item->created_at,
                    'files' => !empty($item->files) ? (new FileCollection($item->files))->resolve() : [],
                ];
            })->toArray() : []
        ];
    }
}
