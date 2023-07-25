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
                    'user' => ($this->names_publication ? $this->names : __('custom.anonymous_applicant') ),
                    'subject' => $this->responseSubject->subject_name,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'my_title' => __('custom.own_application_system_title',
                [
                    'subject' => $this->responseSubject->subject_name,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'response_subject_name' => $this->responseSubject->subject_name,
            'response' => $this->response,
            'response_date' => displayDate($this->response_date),
            'request' => $this->request,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'subject' => $this->responseSubject->subject_name,
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
                                    : ($this->names_publication ? $this->names : __('custom.anonymous_applicant') ) )
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
                        'text' => $item->add_text,
                        'files' => (new FileCollection($item->files))->resolve(),
                    ];
                })->toArray() : [],
            'cnt_visits' => (int)$this->number_of_visits
        ];
    }
}
