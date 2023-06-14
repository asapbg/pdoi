<?php

namespace App\Http\Resources;

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
            'request' => $this->request,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'statusName' => $this->statusName,
            'subject' => $this->responseSubject->subject_name,
        ];
    }
}
