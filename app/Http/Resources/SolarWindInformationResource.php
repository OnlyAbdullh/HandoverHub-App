<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolarWindInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'solar_type' => $this->solar_type,
            'solar_capacity' => $this->solar_capacity,
            'number_of_panels' => $this->number_of_panels,
            'number_of_modules' => $this->number_of_modules,
            'number_of_faulty_modules' => $this->number_of_faulty_modules,
            'number_of_batteries' => $this->number_of_batteries,
            'battery_type' => $this->battery_type,
            'battery_status' => $this->battery_status,
            'wind_remarks' => $this->wind_remarks,
            'remarks' => $this->remarks,
        ];
    }
}
