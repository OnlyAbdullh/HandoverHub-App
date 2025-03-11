<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BandInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'band_type'  => $this->band_type,
            'rbs_1_type' => $this->rbs_1_type,
            'rbs_2_type' => $this->rbs_2_type,
            'du_1_type'  => $this->du_1_type,
            'du_2_type'  => $this->du_2_type,
            'ru_1_type'  => $this->ru_1_type,
            'ru_2_type'  => $this->ru_2_type,
            'remarks'    => $this->remarks,
        ];
    }
}
