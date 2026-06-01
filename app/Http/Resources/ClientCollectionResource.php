<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollectionResource extends ResourceCollection
{

    public $collects = ClientResource::class;

    public function toArray($request): array
    {
        return [
            'data'         => $this->collection,
            'current_page' => $this->resource->currentPage(),
            'last_page'    => $this->resource->lastPage(),
            'total'        => $this->resource->total(),
            'message'      => 'success',
        ];
    }

}
