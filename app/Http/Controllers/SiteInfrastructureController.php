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

            // Store site images
            foreach ($request->file('site_images', []) as $image) {
                $site->images()->create([
                    'image' => $image->store('public/site'),
                    'image_type' => 'original',
                ]);
            }

            foreach ($request->file('site_additional_images', []) as $image) {
                $site->images()->create([
                    'image' => $image->store('public/site'),
                    'image_type' => 'additional',
                ]);
            }

            $relatedEntities = [
                'tower_informations' => 'towerImages',
                'band_informations' => 'bandImages',
                'generator_informations' => 'generatorImages',
                'solar_wind_informations' => 'solarImages',
                'rectifier_informations' => 'rectifierImages' // This will have rectifier images and battery images keys
            ];

            foreach ($relatedEntities as $relation => $imagesKey) {

                $entity = $site->{$relation}()->create($request->input($relation));

                if ($relation === 'rectifierInformation') {
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
                    $folder = str_replace('Information', '', $relation);
                    foreach ($request->file($imagesKey, []) as $image) {
                        $entity->images()->create([
                            'image' => $image->store("public/{$folder}"),
                            'image_type' => 'original',
                        ]);
                    }
                }
            }

            $site->fiberInformation()->create($request->input('fiber_informations'));
            $site->environmentInformation()->create($request->input('environment_informations'));
            $site->lvdpInformation()->create($request->input('lvdp_informations'));
            $site->ampereInformation()->create($request->input('amperes_informations'));
            $site->tcuInformation()->create($request->input('tcu_informations'));

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
