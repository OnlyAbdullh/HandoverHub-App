<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TcuInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'tcu'         => $this->tcu,
            'tcu_types'   => $this->tcu_types_array,
            'remarks'     => $this->remarks,
        ];
    }
}
