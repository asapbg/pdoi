<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PdoiApplicationShortResource extends JsonResource
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
                    'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'my_title' => __('custom.own_application_system_title',
                [
                    'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
                    'apply_date' => displayDate($this->created_at)
                ]),
            'request' => $this->request,
            'response' => $this->response,
            'response_date' => displayDate($this->response_date),
            'created_at' => $this->created_at,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'subject' => $this->response_subject_id ? $this->responseSubject->subject_name : $this->nonRegisteredSubjectName,
            'term' => $this->response_end_time,
            'user_name' => $this->names_publication ? $this->full_names : __('custom.anonymous_applicant'),
            'phone' => $this->phone_publication ? $this->phone : null,
            'email' => $this->email_publication ? $this->email : null,
            'address' => $this->address_publication ? $this->address.($this->address_second ? ', '.$this->address_second : '') : null,
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
                    ];
                })->toArray() : [],
            'final_files' => $this->lastFinalEvent && $this->lastFinalEvent->visibleFiles->count() ? (new FileCollection($this->lastFinalEvent->visibleFiles))->resolve() : [],
            'cnt_visits' => (int)$this->number_of_visits,
            'children' => $this->children->count() ? $this->children->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reg_num' => $item->application_uri,
                    'subject' => $item->response_subject_id ? $item->responseSubject->subject_name : $item->nonRegisteredSubjectName,
                    'date' => displayDateTime($item->created_at),
                    'status' => __('custom.application.status.'. \App\Enums\PdoiApplicationStatusesEnum::keyByValue($item->status))
                ];
            })->toArray() : []
        ];
    }
}
