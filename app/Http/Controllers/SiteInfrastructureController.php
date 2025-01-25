<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteInfrastructureController extends Controller
{
    public function storeAllData(Request $request)
    {
        DB::beginTransaction();

        try {
            $site = Site::create($request->input('sites'));

            foreach ($request->file('site_images', []) as $image) {
                $site->images()->create([
                    'image' => $image->store('public/site/original'),
                    'image_type' => 'original',
                ]);
            }

            foreach ($request->file('site_additional_images', []) as $image) {
                $site->images()->create([
                    'image' => $image->store('public/site/additional'),
                    'image_type' => 'additional',
                ]);
            }

            $relatedEntities = [
                'tower_informations' => 'towerImages',
                'band_informations' => 'bandImages',
                'solar_wind_informations' => 'solarImages',
                'rectifier_informations' => 'rectifierImages'
            ];

            foreach ($relatedEntities as $relation => $imagesKey) {
                if ($request->has($relation)) {
                    $entity = $site->{$relation}()->create($request->input($relation));

                    if ($relation === 'rectifier_informations') {
                        foreach ($request->file('rectifierImages', []) as $image) {
                            $entity->images()->create([
                                'image' => $image->store('public/rectifier/rectifierImages'),
                                'image_type' => 'original',
                            ]);
                        }

                        foreach ($request->file('batteryImages', []) as $image) {
                            $entity->images()->create([
                                'image' => $image->store('public/rectifier/batteryImages'),
                                'image_type' => 'additional', // Differentiate rectifier from battery images
                            ]);
                        }
                    } else {
                        $folder = str_replace('_informations', '', $relation);
                        foreach ($request->file($imagesKey, []) as $image) {
                            $entity->images()->create([
                                'image' => $image->store("public/{$folder}"),
                                'image_type' => 'original',
                            ]);
                        }
                    }
                }
            }

            if ($request->has('generator_informations')) {
                foreach ($request->input('generator_informations') as $generatorInfo) {
                    $site->generator_informations()->create($generatorInfo);
                }
            }

            $site->fiber_informations()->create($request->input('fiber_informations'));
            $site->environment_informations()->create($request->input('environment_informations'));
            $site->lvdp_informations()->create($request->input('lvdp_informations'));
            $site->amperes_informations()->create($request->input('amperes_informations'));

            // Convert tcu_types list into a number using bitwise OR operation.
            // Example: If tcu_types contains [1, 2, 4], the result will be 1 | 2 | 4 = 7.

            $tcuData = $request->input('tcu_informations');
            $state = $tcuData['tcu'];

            if ($state == 1 && isset($tcuData['tcu_types']) && is_array($tcuData['tcu_types'])) {
                $tcuTypeMap = [
                    '2G' => 1,
                    '3G' => 2,
                    'LTE' => 4,
                ];

                $tcuData['tcu_types'] = array_map(function ($type) use ($tcuTypeMap) {
                    return $tcuTypeMap[$type] ?? 0;
                }, $tcuData['tcu_types']);

                $tcuData['tcu_types'] = array_reduce($tcuData['tcu_types'], function ($carry, $type) {
                    return $carry | $type;
                }, 0);
            }
            $site->tcu_informations()->create($tcuData);

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
