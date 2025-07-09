<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RectifierInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'rectifier_1_type_and_voltage' => $this->rectifier_1_type_and_voltage,
            'rectifier_2_type_and_voltage' => $this->rectifier_2_type_and_voltage,
            'module_1_quantity'            => $this->module_1_quantity,
            'module_2_quantity'            => $this->module_2_quantity,
            'faulty_module_1_quantity'     => $this->faulty_module_1_quantity,
            'faulty_module_2_quantity'     => $this->faulty_module_2_quantity,
            'number_of_batteries'          => $this->number_of_batteries,
            'battery_type'                 => $this->battery_type,
            'batteries_cabinet_type'       => $this->batteries_cabinet_type,
            'cabinet_cage'                 => (bool) $this->cabinet_cage,
            'batteries_status'             => $this->batteries_status,
            'remarks'                      => $this->remarks,
        ];
    }
}
