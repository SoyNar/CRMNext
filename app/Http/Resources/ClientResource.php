<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nit' => $this->nit,
            'name'           => $this->name,
            'phone'          => $this->phone,
            'url_page'       => $this->url_page,
            'status'         => $this->status,
            'contacts_count' => $this->contacts_count ?? 0,
            'creator'        => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
