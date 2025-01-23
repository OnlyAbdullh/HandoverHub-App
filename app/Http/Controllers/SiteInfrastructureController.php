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
                    'image' => $image->store('public/site'),
                    'image_type' => 'original',
                ]);
            }

            // Handle additional site images
            foreach ($request->file('site_additional_images', []) as $image) {
                $site->images()->create([
                    'image' => $image->store('public/site'),
                    'image_type' => 'additional',
                ]);
            }

            $relatedEntities = [
                'tower_informations' => 'towerImages',
                'band_informations' => 'bandImages',
                'solar_wind_informations' => 'solarImages',
                'rectifier_informations' => 'rectifierImages' // This will have rectifier images and battery images keys
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

            // Handle other related entities
            $site->fiber_informations()->create($request->input('fiber_informations'));
            $site->environment_informations()->create($request->input('environment_informations'));
            $site->lvdp_informations()->create($request->input('lvdp_informations'));
            $site->amperes_informations()->create($request->input('amperes_informations'));
            $site->tcu_informations()->create($request->input('tcu_informations'));

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
