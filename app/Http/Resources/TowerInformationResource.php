<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TowerInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'mast' =>(bool) $this->mast,
            'tower' => (bool) $this->tower,
            'monopole' => (bool) $this->monopole,
            'mast_number' =>$this->mast_number,
            'mast_status' => $this->mast_status,
            'tower_number' => $this->tower_number,
            'tower_status' => $this->tower_status,
            'beacon_status' => $this->beacon_status,
            'monopole_number' => (int)$this->monopole_number,
            'monopole_status' => $this->monopole_status,
            'mast_1_height' => $this->mast_1_height,
            'mast_2_height' => $this->mast_2_height,
            'mast_3_height' => $this->mast_3_height,
            'tower_1_height' => $this->tower_1_height,
            'tower_2_height' => $this->tower_2_height,
            'monopole_height' => $this->monopole_height,
            'remarks' => $this->remarks,
        ];
    }
}
