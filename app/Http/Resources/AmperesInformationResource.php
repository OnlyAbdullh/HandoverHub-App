<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmperesInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'capacity'     => $this->capacity,
            'time'         => $this->time,
            'cable_length' => $this->cable_length,
            'details'      => $this->details,
        ];
    }
}
