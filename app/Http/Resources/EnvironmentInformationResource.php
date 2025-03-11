<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnvironmentInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'power_control_serial_number' => $this->power_control_serial_number,
            'ampere_consumption'           => $this->ampere_consumption,
            'mini_phase'                 => $this->mini_phase,
            'three_phase'                => $this->three_phase,
            'power_control_ownership'    => $this->power_control_ownership,
            'fan_quantity'               => $this->fan_quantity,
            'faulty_fan_quantity'        => $this->faulty_fan_quantity,
            'earthing_system'            => $this->earthing_system,
            'air_conditioner_1_type'     => $this->air_conditioner_1_type,
            'air_conditioner_2_type'     => $this->air_conditioner_2_type,
            'stabilizer_quantity'        => $this->stabilizer_quantity,
            'stabilizer_type'            => $this->stabilizer_type,
            'exiting'                    => $this->exiting,
            'working'                    => $this->working,
            'remarks'                    => $this->remarks,
        ];
    }
}
