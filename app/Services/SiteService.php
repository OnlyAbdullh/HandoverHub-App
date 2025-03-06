<?php

namespace App\Services;

use App\Repositories\SiteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteService
{
    protected $siteRepository;

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function storeAllData(Request $request)
    {
        DB::beginTransaction();

        try {
            $siteData = $request->input('sites', []);
            if ($this->siteRepository->siteExists($siteData['code'] ?? '')) {
                return response()->json(['message' => 'This code already entered'], 400);
            }

            $site = $this->siteRepository->createSite($siteData);

            // Store site images
            $this->siteRepository->storeImages($site, $request->file('general_site_images', []), 'site/original');
            $this->siteRepository->storeImages($site, $request->file('additional_images', []), 'site/additional', 'additional');
            $this->siteRepository->storeImages($site, $request->file('transmission_images', []), 'transmission', 'transmission');
            $this->siteRepository->storeImages($site, $request->file('fuel_cage_images', []), 'fuel_cage', 'fuel_cage');

            // Define related entities mapping
            $relatedEntities = [
                'tower_informations'      => 'tower_images',
                'band_informations'       => 'rbs_images',
                'solar_wind_informations' => 'solar_and_wind_batteries_images',
                'rectifier_informations'  => [
                    'rectifier_images'           => 'rectifier/rectifierImages',
                    'rectifier_batteries_images' => 'rectifier/batteryImages',
                ],
                'generator_informations'  => 'generator_images',
            ];

            // Store related entities and their images
            foreach ($relatedEntities as $relation => $imagesKey) {
                $data  = $request->input($relation, []);
                $files = $request->allFiles();
                $this->siteRepository->storeRelatedEntity($site, $relation, $imagesKey, $data, $files);
            }

            // Store additional information if provided
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
                $this->siteRepository->storeTcuInformation($site, $request->input('tcu_informations'));
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
}
