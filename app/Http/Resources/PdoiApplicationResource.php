<?php

namespace App\Http\Resources;

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
            'response_subject_name' => $this->responseSubject->subject_name,
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
            'area' => $this->area->ime,
            'municipality' => $this->municipality->ime,
            'settlement' => $this->settlement->ime,
            'post_code' => $this->settlement->post_code,
            'address' => $this->address,
            'address_second' => $this->address_second,
            'files' => (new FileCollection($this->files))->resolve(),
            'themes' => $this->categories->count() ?
                $this->categories->map(function ($item) {
                    return $item->name;
                })->toArray() : [],
        ];
    }
}
