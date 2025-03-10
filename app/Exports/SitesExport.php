<?php

namespace App\Exports;

use App\Models\Site;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SitesExport implements FromCollection, WithHeadings
{
    protected $siteIds;

    public function __construct(array $siteIds)
    {
        $this->siteIds = $siteIds;
    }

    public function collection(): Collection
    {
        $sites = Site::with([
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
        ])->whereIn('id', $this->siteIds)
            ->get();

        $rows = [];
        foreach ($sites as $site) {
            //[1] site_information
            $rowData = [
                'site_name' => $site->name,
                'site_code' => $site->code,
                'site_governorate' => $site->governorate,
                'site_street' => $site->street,
                'site_area' => $site->area,
                'site_city' => $site->city,
                'site_type' => $site->type,
                'site_gsm1900' => $site->gsm1900 ? 'Yes' : 'No',
                'site_gsm1800' => $site->gsm1800 ? 'Yes' : 'No',
                'site_3g' => $site->three_g ? 'Yes' : 'No',
                'site_lte' => $site->lte ? 'Yes' : 'No',
                'site_generator' => $site->generator ? 'Yes' : 'No',
                'site_solar' => $site->solar ? 'Yes' : 'No',
                'site_wind' => $site->wind ? 'Yes' : 'No',
                'site_grid' => $site->grid ? 'Yes' : 'No',
                'site_fence' => $site->fence ? 'Yes' : 'No',
                'site_cabinet_number' => $site->cabinet_number,
                'site_electricity_meter' => $site->electricity_meter,
                'site_electricity_meter_reading' => $site->electricity_meter_reading,
                'site_generator_remark' => $site->generator_remark,
            ];

            // [2] tower_informations
            $tower = optional($site->tower_informations);
            $rowData = array_merge($rowData, [
                'tower_mast' => $tower->mast ? 'Yes' : 'No',
                'tower_tower' => $tower->tower ? 'Yes' : 'No',
                'tower_monopole' => $tower->monopole ? 'Yes' : 'No',
                'tower_mast_number' => $tower->mast_number,
                'tower_mast_status' => $tower->mast_status,
                'tower_tower_number' => $tower->tower_number,
                'tower_tower_status' => $tower->tower_status,
                'tower_beacon_status' => $tower->beacon_status,
                'tower_monopole_number' => $tower->monopole_number,
                'tower_monopole_status' => $tower->monopole_status,
                'tower_mast_1_height' => $tower->mast_1_height,
                'tower_mast_2_height' => $tower->mast_2_height,
                'tower_mast_3_height' => $tower->mast_3_height,
                'tower_1_height' => $tower->tower_1_height,
                'tower_2_height' => $tower->tower_2_height,
                'tower_monopole_height' => $tower->monopole_height,
                'tower_remarks' => $tower->remarks,
            ]);

            // [3] band_informations (One-To-Many)
            $bands = $site->band_informations->filter(function ($band) {
                return collect($band->getAttributes())
                    ->except(['id', 'site_id'])
                    ->filter()
                    ->isNotEmpty();
            });

            if ($bands->isNotEmpty()) {
                $rowData['band_informations'] = $bands->map(function ($band) {
                    return [
                        'band_type'      => $band->band_type,
                        'band_rbs_1_type'=> $band->rbs_1_type,
                        'band_rbs_2_type'=> $band->rbs_2_type,
                        'band_du_1_type' => $band->du_1_type,
                        'band_du_2_type' => $band->du_2_type,
                        'band_ru_1_type' => $band->ru_1_type,
                        'band_ru_2_type' => $band->ru_2_type,
                        'band_remarks'   => $band->remarks,
                    ];
                })->values()->toArray();
            }

            // [4] generator_informations (One-To-Many)
            $generators = $site->generator_informations;
            $generatorsData = $generators->map(function ($gen, $index) {
                return "Generator #" . ($index + 1) . ": "
                    . "gen_type_and_capacity={$gen->gen_type_and_capacity}, "
                    . "gen_hour_meter={$gen->gen_hour_meter}, "
                    . "gen_fuel_consumption={$gen->gen_fuel_consumption}, "
                    . "internal_capacity={$gen->internal_capacity}, "
                    . "internal_existing_fuel={$gen->internal_existing_fuel}, "
                    . "internal_cage=" . ($gen->internal_cage ? 'Yes' : 'No') . ", "
                    . "external_capacity={$gen->external_capacity}, "
                    . "external_existing_fuel={$gen->external_existing_fuel}, "
                    . "external_cage=" . ($gen->external_cage ? 'Yes' : 'No') . ", "
                    . "fuel_sensor_exiting=" . ($gen->fuel_sensor_exiting ? 'Yes' : 'No') . ", "
                    . "fuel_sensor_working=" . ($gen->fuel_sensor_working ? 'Yes' : 'No') . ", "
                    . "fuel_sensor_type={$gen->fuel_sensor_type}, "
                    . "ampere_to_owner={$gen->ampere_to_owner}, "
                    . "circuit_breakers_quantity={$gen->circuit_breakers_quantity}";
            })->implode(" || ");
            $rowData['generator_informations'] = $generatorsData;

            // [5] solar_wind_informations
            $solarWind = optional($site->solar_wind_informations);
            $rowData = array_merge($rowData, [
                'solar_type' => $solarWind->solar_type,
                'solar_capacity' => $solarWind->solar_capacity,
                'solar_number_of_panels' => $solarWind->number_of_panels,
                'solar_number_of_modules' => $solarWind->number_of_modules,
                'solar_number_of_faulty_modules' => $solarWind->number_of_faulty_modules,
                'solar_number_of_batteries' => $solarWind->number_of_batteries,
                'solar_battery_type' => $solarWind->battery_type,
                'solar_battery_status' => $solarWind->battery_status,
                'wind_remarks' => $solarWind->wind_remarks,
                'solar_wind_remarks' => $solarWind->remarks,
            ]);

            // [6] rectifier_informations
            $rectifier = optional($site->rectifier_informations);
            $rowData = array_merge($rowData, [
                'rectifier_1_type_and_voltage' => $rectifier->rectifier_1_type_and_voltage,
                'rectifier_2_type_and_voltage' => $rectifier->rectifier_2_type_and_voltage,
                'rectifier_module_1_quantity' => $rectifier->module_1_quantity,
                'rectifier_module_2_quantity' => $rectifier->module_2_quantity,
                'rectifier_faulty_module_1_quantity' => $rectifier->faulty_module_1_quantity,
                'rectifier_faulty_module_2_quantity' => $rectifier->faulty_module_2_quantity,
                'rectifier_number_of_batteries' => $rectifier->number_of_batteries,
                'rectifier_battery_type' => $rectifier->battery_type,
                'rectifier_batteries_cabinet_type' => $rectifier->batteries_cabinet_type,
                'rectifier_cabinet_cage' => $rectifier->cabinet_cage ? 'Yes' : 'No',
                'rectifier_batteries_status' => $rectifier->batteries_status,
                'rectifier_remarks' => $rectifier->remarks,
            ]);

            // [7] environment_informations
            $env = optional($site->environment_informations);
            $rowData = array_merge($rowData, [
                'environment_power_control_serial_number' => $env->power_control_serial_number,
                'environment_ampere_consumption' => $env->ampere_consumption,
                'environment_mini_phase' => $env->mini_phase ? 'Yes' : 'No',
                'environment_three_phase' => $env->three_phase ? 'Yes' : 'No',
                'environment_power_control_ownership' => $env->power_control_ownership,
                'environment_fan_quantity' => $env->fan_quantity,
                'environment_faulty_fan_quantity' => $env->faulty_fan_quantity,
                'environment_earthing_system' => $env->earthing_system ? 'Yes' : 'No',
                'environment_air_conditioner_1_type' => $env->air_conditioner_1_type,
                'environment_air_conditioner_2_type' => $env->air_conditioner_2_type,
                'environment_stabilizer_quantity' => $env->stabilizer_quantity,
                'environment_stabilizer_type' => $env->stabilizer_type,
                'environment_exiting' => $env->exiting ? 'Yes' : 'No',
                'environment_working' => $env->working ? 'Yes' : 'No',
                'environment_remarks' => $env->remarks,
            ]);

            // [8] lvdp_informations
            $lvdp = optional($site->lvdp_informations);
            $rowData = array_merge($rowData, [
                'lvdp_exiting' => $lvdp->exiting ? 'Yes' : 'No',
                'lvdp_working' => $lvdp->working ? 'Yes' : 'No',
                'lvdp_status' => $lvdp->status,
                'lvdp_remarks' => $lvdp->remarks,
            ]);

            // [9] fiber_informations
            $fiber = optional($site->fiber_informations);
            $rowData = array_merge($rowData, [
                'fiber_destination' => $fiber->destination,
                'fiber_remarks' => $fiber->remarks,
            ]);

            // [10] amperes_informations
            $ampereInfo = optional($site->amperes_informations);
            $rowData = array_merge($rowData, [
                'amperes_capacity' => $ampereInfo->capacity,
                'amperes_time' => $ampereInfo->time,
                'amperes_cable_length' => $ampereInfo->cable_length,
                'amperes_details' => $ampereInfo->details,
            ]);

            // [11] tcu_informations
            $tcu = optional($site->tcu_informations);
            $rowData = array_merge($rowData, [
                'tcu' => $tcu->tcu ? 'Yes' : 'No',
                'tcu_types' => is_array($tcu->tcu_types)
                    ? implode(',', $tcu->tcu_types)
                    : $tcu->tcu_types,
                'tcu_remarks' => $tcu->remarks,
            ]);

            $rows[] = $rowData;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
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

            'band_type',
            'band_rbs_1_type',
            'band_rbs_2_type',
            'band_du_1_type',
            'band_du_2_type',
            'band_ru_1_type',
            'band_ru_2_type',
            'band_remarks',

            'gen_type_and_capacity',
            'gen_hour_meter',
            'gen_fuel_consumption',
            'internal_capacity',
            'internal_existing_fuel',
            'internal_cage',
            'external_capacity',
            'external_existing_fuel',
            'external_cage',
            'fuel_sensor_exiting',
            'fuel_sensor_working',
            'fuel_sensor_type',
            'ampere_to_owner',
            'circuit_breakers_quantity',

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

            'lvdp_exiting',
            'lvdp_working',
            'lvdp_status',
            'lvdp_remarks',

            'fiber_destination',
            'fiber_remarks',

            'amperes_capacity',
            'amperes_time',
            'amperes_cable_length',
            'amperes_details',

            'tcu',
            'tcu_types',
            'tcu_remarks',
        ];
    }
}
