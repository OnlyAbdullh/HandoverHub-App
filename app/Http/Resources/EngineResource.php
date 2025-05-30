<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'engine_brand' => [
                'id' => $this->brand->id,
                'brand' => $this->brand->name
            ],
            'engine_capacity' => [
                'id' => $this->capacity->id,
                'capacity' => $this->capacity->value
            ]
        ];
    }
}
