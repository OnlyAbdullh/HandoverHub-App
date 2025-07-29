<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Carbon\Carbon;
class ReplacedPartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->part?->id,
            'name'                  => $this->part?->name,
            'quantity'              => $this->quantity,
            'faulty_quantity'       => $this->faulty_quantity,
            'code'                  => $this->part?->code,
            'note'                  => $this->notes,
            'reason'                => $this->reason,
            'is_faulty'             => (bool) $this->is_faulty,
            'last_replacement_date' => $this->last_part_usage
                ? Carbon::parse(Arr::get($this->last_part_usage, 'visit_date'))->format('Y-m-d')
                : null,
            'generator_hours_at_last_replacement' => Arr::get($this->last_part_usage, 'current_reading'),


        ];
    }
}
