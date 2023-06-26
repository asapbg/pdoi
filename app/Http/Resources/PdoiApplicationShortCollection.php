<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PdoiApplicationShortCollection extends ResourceCollection
{
    private $pagination;
    private $links;

    public function __construct($resource)

    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'total_pages' => $resource->lastPage()
        ];
        $this->links = $resource->withQueryString()->links();

        $resource = $resource->getCollection();

        parent::__construct($resource);

    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => (PdoiApplicationShortResource::collection($this->collection))->resolve(),
            'pagination' => $this->pagination,
            'links' => $this->links
        ];
    }

}
