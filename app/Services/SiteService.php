<?php

namespace App\Services;

use App\Repositories\SiteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SiteService
{
    protected $siteRepository;
    protected array $allowedImageTypes = [
        'original',
        'additional',
        'transmission',
        'fuel_cage'
    ];

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

            $this->siteRepository->storeImages($site, $request->file('general_site_images', []), 'site/original');
            $this->siteRepository->storeImages($site, $request->file('additional_images', []), 'site/additional', 'additional');
            $this->siteRepository->storeImages($site, $request->file('transmission_images', []), 'transmission', 'transmission');
            $this->siteRepository->storeImages($site, $request->file('fuel_cage_images', []), 'fuel_cage', 'fuel_cage');

            $relatedEntities = [
                'tower_informations' => 'tower_images',
                'solar_wind_informations' => 'solar_and_wind_batteries_images',
                'rectifier_informations' => [
                    'rectifier_images' => 'rectifier/rectifierImages',
                    'rectifier_batteries_images' => 'rectifier/batteryImages',
                ],
            ];
            foreach ($relatedEntities as $relation => $imagesKey) {
                $data = $request->input($relation, []);
                $files = $request->allFiles();
                $this->siteRepository->storeRelatedEntity($site, $relation, $imagesKey, $data, $files);
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
                $this->siteRepository->storeTcuInformation($site, $request->input('tcu_informations'));
            }

            $relations = [
                'generator_informations' => 'generator_images',
                'band_informations' => 'rbs_images',
            ];

            foreach ($relations as $relation => $fileKey) {
                if ($request->has($relation)) {
                    foreach ($request->input($relation) as $info) {
                        $filtered = array_filter($info, function ($value) {
                            return !is_null($value) && $value !== '';
                        });
                        if (!empty($filtered) || $request->hasFile($fileKey)) {
                            $site->{$relation}()->create($filtered);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllSites()
    {
        return $this->siteRepository->getAllSites();
    }

    public function deleteSites(array $siteIds): int
    {
        DB::beginTransaction();
        try {
            $deletedCount = $this->siteRepository->deleteSites($siteIds);
            DB::commit();
            return $deletedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getSiteDetails(int $siteId)
    {
        return $this->siteRepository->getSiteDetails($siteId);
    }
    public function getSiteImages(int $siteId, string $imageType)
    {
        if (!in_array($imageType, $this->allowedImageTypes)) {
            throw new Exception('the type of the images is not true');
        }
        $images = $this->siteRepository->getSiteImages($siteId, $imageType);
        return $images;
    }
}
