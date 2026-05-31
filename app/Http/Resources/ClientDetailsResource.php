<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'nit' => $this->nit,
            'id' => $this->id,
            'phone' => $this->phone,
            'email' => $this->email,
            'url_page'       => $this->url_page,
            'status'         => $this->status,
            'creator'        => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ],
            'created_at' => $this->created_at,
            'contacts' => ContactResource::collection($this->contacts),
        ];
    }
}
