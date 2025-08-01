<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'is_general' => $this->is_general,
            'is_primary' =>(bool) $this->is_primary,
            'engines' => $this->when(
                !$request->routeIs('engines.parts'),
                EngineResource::collection($this->whenLoaded('engines'))
            ),
        ];
    }
}
