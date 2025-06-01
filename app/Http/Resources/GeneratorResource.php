<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneratorResource extends JsonResource
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
            'brand' => new BrandResource($this->brand),
            'engine' => new EngineResource($this->engine),
            'initial_meter' => $this->initial_meter ?? '',
            'site' => new MtnSiteResource($this->mtn_site),
        ];
    }
}
