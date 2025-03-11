<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $bands = $this->whenLoaded('band_informations');
        return [
            'sites' => [
                'name' => $this->name,
                'code' => $this->code,
                'governorate' => $this->governorate,
                'street' => $this->street,
                'area' => $this->area,
                'city' => $this->city,
                'type' => $this->type,
                'gsm1900' => $this->gsm1900,
                'gsm1800' => $this->gsm1800,
                '3g' => $this->{'3g'},
                'lte' => $this->lte,
                'generator' => $this->generator,
                'solar' => $this->solar,
                'wind' => $this->wind,
                'grid' => $this->grid,
                'fence' => $this->fence,
                'cabinet_number' => $this->cabinet_number,
                'electricity_meter' => $this->electricity_meter,
                'electricity_meter_reading' => $this->electricity_meter_reading,
                'generator_remark' => $this->generator_remark,
            ],
            'tower_informations' => new TowerInformationResource($this->whenLoaded('tower_informations')),
            'band_informations' => [
                'GSM_900'   => $bands ? new BandInformationResource($bands->firstWhere('band_type', 'GSM 900')) : null,
                'GSM_1800'  => $bands ? new BandInformationResource($bands->firstWhere('band_type', 'GSM 1800')) : null,
                '3G'        => $bands ? new BandInformationResource($bands->firstWhere('band_type', '3G')) : null,
                'LTE'       => $bands ? new BandInformationResource($bands->firstWhere('band_type', 'LTE')) : null,
            ],
            'generator_informations' => GeneratorInformationResource::collection($this->whenLoaded('generator_informations')),
            'solar_wind_informations' => new SolarWindInformationResource($this->whenLoaded('solar_wind_informations')),
            'rectifier_informations' => new RectifierInformationResource($this->whenLoaded('rectifier_informations')),
            'environment_informations' => new EnvironmentInformationResource($this->whenLoaded('environment_informations')),
            'lvdp_informations' => new LvdpInformationResource($this->whenLoaded('lvdp_informations')),
            'fiber_informations' => new FiberInformationResource($this->whenLoaded('fiber_informations')),
            'amperes_informations' => new AmperesInformationResource($this->whenLoaded('amperes_informations')),
            'tcu_informations' => new TcuInformationResource($this->whenLoaded('tcu_informations')),
        ];
    }
}
