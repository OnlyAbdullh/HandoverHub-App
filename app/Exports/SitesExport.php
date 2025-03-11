<?php

namespace App\Exports;

use App\Models\Site;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SitesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $siteIds;
    protected $sites;

    protected $maxBands = 4;
    protected $maxGenerators = 2;

    public function __construct(array $siteIds)
    {
        $this->siteIds = $siteIds;
        $this->sites = Site::with([
            'tower_informations',
            'band_informations',
            'generator_informations',
            'solar_wind_informations',
            'rectifier_informations',
            'environment_informations',
            'lvdp_informations',
            'fiber_informations',
            'amperes_informations',
            'tcu_informations',
        ])
            ->whereIn('id', $this->siteIds)
            ->get();
    }

    public function headings(): array
    {
        $headings = [
            'site_name',
            'site_code',
            'site_governorate',
            'site_street',
            'site_area',
            'site_city',
            'site_type',
            'site_gsm1900',
            'site_gsm1800',
            'site_3g',
            'site_lte',
            'site_generator',
            'site_solar',
            'site_wind',
            'site_grid',
            'site_fence',
            'site_cabinet_number',
            'site_electricity_meter',
            'site_electricity_meter_reading',
            'site_generator_remark',

            // tower_informations
            'tower_mast',
            'tower_tower',
            'tower_monopole',
            'tower_mast_number',
            'tower_mast_status',
            'tower_tower_number',
            'tower_tower_status',
            'tower_beacon_status',
            'tower_monopole_number',
            'tower_monopole_status',
            'tower_mast_1_height',
            'tower_mast_2_height',
            'tower_mast_3_height',
            'tower_1_height',
            'tower_2_height',
            'tower_monopole_height',
            'tower_remarks',

            // solar_wind_informations
            'solar_type',
            'solar_capacity',
            'solar_number_of_panels',
            'solar_number_of_modules',
            'solar_number_of_faulty_modules',
            'solar_number_of_batteries',
            'solar_battery_type',
            'solar_battery_status',
            'wind_remarks',
            'solar_wind_remarks',

            // rectifier_informations
            'rectifier_1_type_and_voltage',
            'rectifier_2_type_and_voltage',
            'rectifier_module_1_quantity',
            'rectifier_module_2_quantity',
            'rectifier_faulty_module_1_quantity',
            'rectifier_faulty_module_2_quantity',
            'rectifier_number_of_batteries',
            'rectifier_battery_type',
            'rectifier_batteries_cabinet_type',
            'rectifier_cabinet_cage',
            'rectifier_batteries_status',
            'rectifier_remarks',

            // environment_informations
            'environment_power_control_serial_number',
            'environment_ampere_consumption',
            'environment_mini_phase',
            'environment_three_phase',
            'environment_power_control_ownership',
            'environment_fan_quantity',
            'environment_faulty_fan_quantity',
            'environment_earthing_system',
            'environment_air_conditioner_1_type',
            'environment_air_conditioner_2_type',
            'environment_stabilizer_quantity',
            'environment_stabilizer_type',
            'environment_exiting',
            'environment_working',
            'environment_remarks',

            // lvdp_informations
            'lvdp_exiting',
            'lvdp_working',
            'lvdp_status',
            'lvdp_remarks',

            // fiber_informations
            'fiber_destination',
            'fiber_remarks',

            // amperes_informations
            'amperes_capacity',
            'amperes_time',
            'amperes_cable_length',
            'amperes_details',

            // tcu_informations
            'tcu',
            'tcu_types',
            'tcu_remarks',
        ];

        for ($i = 1; $i <= $this->maxBands; $i++) {
            $headings[] = "band_type_{$i}";
            $headings[] = "band_rbs_1_type_{$i}";
            $headings[] = "band_rbs_2_type_{$i}";
            $headings[] = "band_du_1_type_{$i}";
            $headings[] = "band_du_2_type_{$i}";
            $headings[] = "band_ru_1_type_{$i}";
            $headings[] = "band_ru_2_type_{$i}";
            $headings[] = "band_remarks_{$i}";
        }

        for ($i = 1; $i <= $this->maxGenerators; $i++) {
            $headings[] = "gen_type_and_capacity_{$i}";
            $headings[] = "gen_hour_meter_{$i}";
            $headings[] = "gen_fuel_consumption_{$i}";
            $headings[] = "internal_capacity_{$i}";
            $headings[] = "internal_existing_fuel_{$i}";
            $headings[] = "internal_cage_{$i}";
            $headings[] = "external_capacity_{$i}";
            $headings[] = "external_existing_fuel_{$i}";
            $headings[] = "external_cage_{$i}";
            $headings[] = "fuel_sensor_exiting_{$i}";
            $headings[] = "fuel_sensor_working_{$i}";
            $headings[] = "fuel_sensor_type_{$i}";
            $headings[] = "ampere_to_owner_{$i}";
            $headings[] = "circuit_breakers_quantity_{$i}";
        }

        return $headings;
    }

    /**
     * بناء الصفوف (كل صف يمثل موقع مع بياناته وتفاصيل العلاقات)
     */
    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->sites as $site) {

            $rowData = [
                'site_name' => $site->name ?? '',
                'site_code' => $site->code ?? '',
                'site_governorate' => $site->governorate ?? '',
                'site_street' => $site->street ?? '',
                'site_area' => $site->area ?? '',
                'site_city' => $site->city ?? '',
                'site_type' => $site->type ?? '',
                'site_gsm1900' => $site->gsm1900 ? 'Yes' : 'No',
                'site_gsm1800' => $site->gsm1800 ? 'Yes' : 'No',
                'site_3g' => $site->three_g ? 'Yes' : 'No',
                'site_lte' => $site->lte ? 'Yes' : 'No',
                'site_generator' => $site->generator ? 'Yes' : 'No',
                'site_solar' => $site->solar ? 'Yes' : 'No',
                'site_wind' => $site->wind ? 'Yes' : 'No',
                'site_grid' => $site->grid ? 'Yes' : 'No',
                'site_fence' => $site->fence ? 'Yes' : 'No',
                'site_cabinet_number' => $site->cabinet_number ?? '',
                'site_electricity_meter' => $site->electricity_meter ?? '',
                'site_electricity_meter_reading' => $site->electricity_meter_reading ?? '',
                'site_generator_remark' => $site->generator_remark ?? '',

                'tower_mast' => $site->tower_informations?->mast ? 'Yes' : 'No',
                'tower_tower' => $site->tower_informations?->tower ? 'Yes' : 'No',
                'tower_monopole' => $site->tower_informations?->monopole ? 'Yes' : 'No',
                'tower_mast_number' => $site->tower_informations?->mast_number ?? '',
                'tower_mast_status' => $site->tower_informations?->mast_status ?? '',
                'tower_tower_number' => $site->tower_informations?->tower_number ?? '',
                'tower_tower_status' => $site->tower_informations?->tower_status ?? '',
                'tower_beacon_status' => $site->tower_informations?->beacon_status ?? '',
                'tower_monopole_number' => $site->tower_informations?->monopole_number ?? '',
                'tower_monopole_status' => $site->tower_informations?->monopole_status ?? '',
                'tower_mast_1_height' => $site->tower_informations?->mast_1_height ?? '',
                'tower_mast_2_height' => $site->tower_informations?->mast_2_height ?? '',
                'tower_mast_3_height' => $site->tower_informations?->mast_3_height ?? '',
                'tower_1_height' => $site->tower_informations?->tower_1_height ?? '',
                'tower_2_height' => $site->tower_informations?->tower_2_height ?? '',
                'tower_monopole_height' => $site->tower_informations?->monopole_height ?? '',
                'tower_remarks' => $site->tower_informations?->remarks ?? '',
            ];

            $solarWind = optional($site->solar_wind_informations);
            $rowData = array_merge($rowData, [
                'solar_type' => $solarWind->solar_type ?? '',
                'solar_capacity' => $solarWind->solar_capacity ?? '',
                'solar_number_of_panels' => $solarWind->number_of_panels ?? '',
                'solar_number_of_modules' => $solarWind->number_of_modules ?? '',
                'solar_number_of_faulty_modules' => $solarWind->number_of_faulty_modules ?? '',
                'solar_number_of_batteries' => $solarWind->number_of_batteries ?? '',
                'solar_battery_type' => $solarWind->battery_type ?? '',
                'solar_battery_status' => $solarWind->battery_status ?? '',
                'wind_remarks' => $solarWind->wind_remarks ?? '',
                'solar_wind_remarks' => $solarWind->remarks ?? '',
            ]);

            $rectifier = optional($site->rectifier_informations);
            $rowData = array_merge($rowData, [
                'rectifier_1_type_and_voltage' => $rectifier->rectifier_1_type_and_voltage ?? '',
                'rectifier_2_type_and_voltage' => $rectifier->rectifier_2_type_and_voltage ?? '',
                'rectifier_module_1_quantity' => $rectifier->module_1_quantity ?? '',
                'rectifier_module_2_quantity' => $rectifier->module_2_quantity ?? '',
                'rectifier_faulty_module_1_quantity' => $rectifier->faulty_module_1_quantity ?? '',
                'rectifier_faulty_module_2_quantity' => $rectifier->faulty_module_2_quantity ?? '',
                'rectifier_number_of_batteries' => $rectifier->number_of_batteries ?? '',
                'rectifier_battery_type' => $rectifier->battery_type ?? '',
                'rectifier_batteries_cabinet_type' => $rectifier->batteries_cabinet_type ?? '',
                'rectifier_cabinet_cage' => $rectifier->cabinet_cage ? 'Yes' : 'No',
                'rectifier_batteries_status' => $rectifier->batteries_status ?? '',
                'rectifier_remarks' => $rectifier->remarks ?? '',
            ]);

            $env = optional($site->environment_informations);
            $rowData = array_merge($rowData, [
                'environment_power_control_serial_number' => $env->power_control_serial_number ?? '',
                'environment_ampere_consumption' => $env->ampere_consumption ?? '',
                'environment_mini_phase' => $env->mini_phase ? 'Yes' : 'No',
                'environment_three_phase' => $env->three_phase ? 'Yes' : 'No',
                'environment_power_control_ownership' => $env->power_control_ownership ?? '',
                'environment_fan_quantity' => $env->fan_quantity ?? '',
                'environment_faulty_fan_quantity' => $env->faulty_fan_quantity ?? '',
                'environment_earthing_system' => $env->earthing_system ? 'Yes' : 'No',
                'environment_air_conditioner_1_type' => $env->air_conditioner_1_type ?? '',
                'environment_air_conditioner_2_type' => $env->air_conditioner_2_type ?? '',
                'environment_stabilizer_quantity' => $env->stabilizer_quantity ?? '',
                'environment_stabilizer_type' => $env->stabilizer_type ?? '',
                'environment_exiting' => $env->exiting ? 'Yes' : 'No',
                'environment_working' => $env->working ? 'Yes' : 'No',
                'environment_remarks' => $env->remarks ?? '',
            ]);

            $lvdp = optional($site->lvdp_informations);
            $rowData = array_merge($rowData, [
                'lvdp_exiting' => $lvdp->exiting ? 'Yes' : 'No',
                'lvdp_working' => $lvdp->working ? 'Yes' : 'No',
                'lvdp_status' => $lvdp->status ?? '',
                'lvdp_remarks' => $lvdp->remarks ?? '',
            ]);

            $fiber = optional($site->fiber_informations);
            $rowData = array_merge($rowData, [
                'fiber_destination' => $fiber->destination ?? '',
                'fiber_remarks' => $fiber->remarks ?? '',
            ]);

            $ampereInfo = optional($site->amperes_informations);
            $rowData = array_merge($rowData, [
                'amperes_capacity' => $ampereInfo->capacity ?? '',
                'amperes_time' => $ampereInfo->time ?? '',
                'amperes_cable_length' => $ampereInfo->cable_length ?? '',
                'amperes_details' => $ampereInfo->details ?? '',
            ]);

            $tcu = optional($site->tcu_informations);
            $rowData = array_merge($rowData, [
                'tcu' => $tcu->tcu ? 'Yes' : 'No',
                'tcu_types' => is_array($tcu->tcu_types) ? implode(',', $tcu->tcu_types) : ($tcu->tcu_types ?? ''),
                'tcu_remarks' => $tcu->remarks ?? '',
            ]);
            $mapping = [
                0 => 'GSM_900',
                1 => 'GSM_1800',
                2 => '3G',
                3 => 'LTE',
            ];
            for ($i = 0; $i < $this->maxBands; $i++) {
                $band = $site->band_informations->get($i);
                $prefix = $mapping[$i] ?? ($i + 1);
                $rowData[$prefix . '_band_type'] = $band ? $band->band_type : '';
                $rowData[$prefix . '_band_rbs_1_type'] = $band ? $band->rbs_1_type : '';
                $rowData[$prefix . '_band_rbs_2_type'] = $band ? $band->rbs_2_type : '';
                $rowData[$prefix . '_band_du_1_type'] = $band ? $band->du_1_type : '';
                $rowData[$prefix . '_band_du_2_type'] = $band ? $band->du_2_type : '';
                $rowData[$prefix . '_band_ru_1_type'] = $band ? $band->ru_1_type : '';
                $rowData[$prefix . '_band_ru_2_type'] = $band ? $band->ru_2_type : '';
                $rowData[$prefix . '_band_remarks'] = $band ? $band->remarks : '';
            }

            for ($i = 0; $i < $this->maxGenerators; $i++) {
                $gen = $site->generator_informations->get($i);
                $rowData["gen_type_and_capacity_" . ($i + 1)] = $gen ? $gen->gen_type_and_capacity : '';
                $rowData["gen_hour_meter_" . ($i + 1)] = $gen ? $gen->gen_hour_meter : '';
                $rowData["gen_fuel_consumption_" . ($i + 1)] = $gen ? $gen->gen_fuel_consumption : '';
                $rowData["internal_capacity_" . ($i + 1)] = $gen ? $gen->internal_capacity : '';
                $rowData["internal_existing_fuel_" . ($i + 1)] = $gen ? $gen->internal_existing_fuel : '';
                $rowData["internal_cage_" . ($i + 1)] = $gen ? ($gen->internal_cage ? 'Yes' : 'No') : '';
                $rowData["external_capacity_" . ($i + 1)] = $gen ? $gen->external_capacity : '';
                $rowData["external_existing_fuel_" . ($i + 1)] = $gen ? $gen->external_existing_fuel : '';
                $rowData["external_cage_" . ($i + 1)] = $gen ? ($gen->external_cage ? 'Yes' : 'No') : '';
                $rowData["fuel_sensor_exiting_" . ($i + 1)] = $gen ? ($gen->fuel_sensor_exiting ? 'Yes' : 'No') : '';
                $rowData["fuel_sensor_working_" . ($i + 1)] = $gen ? ($gen->fuel_sensor_working ? 'Yes' : 'No') : '';
                $rowData["fuel_sensor_type_" . ($i + 1)] = $gen ? $gen->fuel_sensor_type : '';
                $rowData["ampere_to_owner_" . ($i + 1)] = $gen ? $gen->ampere_to_owner : '';
                $rowData["circuit_breakers_quantity_" . ($i + 1)] = $gen ? $gen->circuit_breakers_quantity : '';
            }

            $rows->push($rowData);
        }

        return $rows;
    }

}
