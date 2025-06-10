<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'mtn_site'   => [
                'id'   => $this->mtn_site->id,
                'name' => $this->mtn_site->name,
                'code' => $this->mtn_site->code,
            ],
            'visit_type' => $this->visit_type,
            'visit_date' => $this->visit_date,
        ];
    }
}
