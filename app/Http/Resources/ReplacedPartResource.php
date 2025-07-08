<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplacedPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->part?->id,
            'name'                  => $this->part?->name,
            'quantity'              => $this->quantity,
            'faulty_quantity'       => $this->faulty_quantity,
            'code'                  => $this->part?->code,
            'note'                  => $this->notes,
            'is_faulty'             => (bool) $this->is_faulty,
        ];
    }
}
