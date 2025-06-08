<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'generator'        => new GeneratorResource($this->whenLoaded('generator')),
            'visit_type'       => $this->visit_type,
            'report_number'    => $this->report_number,
            'visit_date'       => $this->visit_date?->toDateString(),
            'visit_time'       => $this->visit_time,
            'oil_pressure'     => $this->oil_pressure,
            'temperature'      => $this->temperature,
            'battery_voltage'  => $this->battery_voltage,
            'oil_quantity'     => $this->oil_quantity,
            'burned_oil_quantity'=> $this->burned_oil_quantity,
            'frequency'        => $this->frequency,
            'meter'            => $this->current_reading,
            'last_meter'       => $this->previous_reading,
            'ats_status'       => $this->link_status,
            'last_visit_date'  => $this->previous_visit_date,
            'volt_l1'          => $this->voltage_L1,
            'volt_l2'          => $this->voltage_L2,
            'volt_l3'          => $this->voltage_L3,
            'load_l1'          => $this->load_L1,
            'load_l2'          => $this->load_L2,
            'load_l3'          => $this->load_L3,
            'visit_resons'     => $this->visit_reason,
            'technician_notes' => TechnicianNoteResource::collection($this->whenLoaded('technicianNotes')),
            'technical_status' => $this->technical_status,
            'completed_works'  => CompletedTaskResource::collection($this->whenLoaded('completedTasks')),
            'visit_location'   => $this->site?->name,
            'parts'            => ReplacedPartResource::collection($this->whenLoaded('replacedParts')),
        ];
    }
}
