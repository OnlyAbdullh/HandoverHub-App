<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LvdpInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'exiting'  => (bool)$this->exiting,
            'working'  => (bool)$this->working,
            'status'   => $this->status,
            'remarks'  => $this->remarks,
        ];
    }
}
