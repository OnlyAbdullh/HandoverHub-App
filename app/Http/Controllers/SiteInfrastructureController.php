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
            $siteData = $request->input('sites', []);
            $exist = Site::where('code', $siteData['code'])->exists();
            if ($exist) {
                return response()->json(['message' => 'This code already entered'], 400);
            }
            $site = Site::create($siteData ?: []);
            $this->storeImages($site, $request->file('general_site_images', []), ' site/original');
            $this->storeImages($site, $request->file('additional_images', []), ' site/additional', 'additional');
            $this->storeImages($site, $request->file('transmission_images', []), ' transmission','transmission');
            $this->storeImages($site, $request->file('fuel_cage_images', []), ' fuel_cage', 'fuel_cage');
            $relatedEntities = [
                'tower_informations' => 'tower_images',
                'band_informations' => 'rbs_images',
                'solar_wind_informations' => 'solar_and_wind_batteries_images',
                'rectifier_informations' => [
                    'rectifier_images' => 'rectifier/rectifierImages',
                    'rectifier_batteries_images' => 'rectifier/batteryImages',
                ],
                'generator_informations' => 'generator_images',
            ];

            foreach ($relatedEntities as $relation => $imagesKey) {
                $this->storeRelatedEntity($site, $relation, $imagesKey, $request);
            }

            if ($request->filled('fiber_informations')) {
                $site->fiber_informations()->create($request->input('fiber_informations'));
            }
            if ($request->filled('environment_informations')) {
                $site->environment_informations()->create($request->input('environment_informations'));
            }
            if ($request->filled('lvdp_informations')) {
                $site->lvdp_informations()->create($request->input('lvdp_informations'));
            }
            if ($request->filled('amperes_informations')) {
                $site->amperes_informations()->create($request->input('amperes_informations'));
            }
            if ($request->filled('tcu_informations')) {
                $this->storeTcuInformation($site, $request->input('tcu_informations'));
            }

            if ($request->has('generator_informations')) {
                foreach ($request->input('generator_informations') as $generatorInfo) {
                    $site->generator_informations()->create($generatorInfo);
                }
            }

            if ($request->has('band_informations')) {
                foreach ($request->input('band_informations') as $bandInfo) {
                    $site->band_informations()->create($bandInfo);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function storeImages($model, $images, $path, $type = 'original')
    {
        foreach ($images as $image) {
            $model->images()->create([
                'image' => $image->store($path, 'public'),
                'type'=>$type
            ]);
        }
    }


    private function storeRelatedEntity($site, $relation, $imagesKey, $request)
    {
        $entity = null;

        if ($request->has($relation)) {
            $entity = $site->{$relation}()->create($request->input($relation));
        }

        if (is_array($imagesKey)) {
            foreach ($imagesKey as $key => $folder) {
                if ($request->hasFile($key)) {
                    $entity = $entity ?: $site->{$relation}()->create([]);
                    $this->storeImages($entity, $request->file($key), $folder);
                }
            }
        } else {
            if ($request->hasFile($imagesKey)) {
                $entity = $entity ?: $site->{$relation}()->create([]);
                $folder = str_replace('_informations', '', $relation);
                $this->storeImages($entity, $request->file($imagesKey), "{$folder}");
            }
        }
    }

    private function storeTcuInformation($site, $tcuData)
    {
        if (isset($tcuData['tcu']) && $tcuData['tcu'] == 1 && isset($tcuData['tcu_types']) && is_array($tcuData['tcu_types'])) {
            $tcuTypeMap = [
                '2G' => 1,
                '3G' => 2,
                'LTE' => 4,
            ];
            $tcuData['tcu_types'] = array_reduce(array_map(function ($type) use ($tcuTypeMap) {
                return $tcuTypeMap[$type] ?? 0;
            }, $tcuData['tcu_types']), function ($carry, $type) {
                return $carry | $type;
            }, 0);
        }
        $site->tcu_informations()->create($tcuData);
    }
}
