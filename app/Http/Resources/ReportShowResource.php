<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

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
            'visit_date'       => $this->visit_date?->format('Y-m-d'),
            'visit_time'       => $this->visit_time->format('H:i:s'),
            'oil_pressure'     => $this->oil_pressure,
            'temperature'      => $this->temperature,
            'battery_voltage'  => $this->battery_voltage,
            'oil_quantity'     => $this->oil_quantity,
            'burned_oil_quantity'=> $this->burned_oil_quantity,
            'frequency'        => $this->frequency,
            'current_meter'    => $this->current_reading,
            'ats_status'       => $this->ats_status,
            'volt_l1'          => $this->voltage_L1,
            'volt_l2'          => $this->voltage_L2,
            'volt_l3'          => $this->voltage_L3,
            'load_l1'          => $this->load_L1,
            'load_l2'          => $this->load_L2,
            'load_l3'          => $this->load_L3,
            'visit_reason'     => $this->visit_reason,
            'longitude'        => $this->longitude,
            'latitude'         => $this->latitude,

            'last_meter' => Arr::get($this->last_routine_visit, 'current_reading'),
            'last_routine_visit_date' => $this->last_routine_visit
                ? Carbon::parse(Arr::get($this->last_routine_visit, 'visit_date'))->format('Y-m-d')
                : null,

            'technician_notes' => TechnicianNoteResource::collection($this->whenLoaded('technicianNotes')),
            'technical_status' => $this->technical_status,
            'completed_works'  => CompletedTaskResource::collection($this->whenLoaded('completedTasks')),
            'parts'            => ReplacedPartResource::collection($this->whenLoaded('replacedParts')),
        ];
    }
}
