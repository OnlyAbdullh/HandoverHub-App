<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneratorInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'gen_type_and_capacity'   => $this->gen_type_and_capacity,
            'gen_hour_meter'          => $this->gen_hour_meter,
            'gen_fuel_consumption'    => $this->gen_fuel_consumption,
            'internal_capacity'       => $this->internal_capacity,
            'internal_existing_fuel'  => $this->internal_existing_fuel,
            'internal_cage'           => $this->internal_cage,
            'external_capacity'       => $this->external_capacity,
            'external_existing_fuel'  => $this->external_existing_fuel,
            'external_cage'           => $this->external_cage,
            'fuel_sensor_exiting'     => $this->fuel_sensor_exiting,
            'fuel_sensor_working'     => $this->fuel_sensor_working,
            'fuel_sensor_type'        => $this->fuel_sensor_type,
            'ampere_to_owner'         => $this->ampere_to_owner,
            'circuit_breakers_quantity'=> $this->circuit_breakers_quantity,
        ];
    }
}
