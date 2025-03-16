<?php

namespace App\Services;

use App\Models\Site;
use App\Repositories\SiteRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

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
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $siteData['user_name'] = $user->username;

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
            $files = $request->allFiles();

            foreach ($relations as $relation => $fileKey) {
                if ($request->has($relation)) {
                    foreach ($request->input($relation) as $info) {
                        $filtered = array_filter($info, function ($value) {
                            return !is_null($value) && $value !== '';
                        });
                        $this->siteRepository->storeRelatedEntity($site, $relation, $fileKey, $filtered, $files);
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
        $user = Auth::user();

        if ($user->hasRole('employee')) {
            return $this->siteRepository->getSitesByUsername($user->username);
        } else {
            return $this->siteRepository->getAllSites();
        }
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
        $user = auth()->user();

        if ($user->hasRole('employee')) {
            $siteUserName = Site::where('id', $siteId)->value('user_name');
            if ($siteUserName !== $user->username) {
                throw new Exception('you do not have the right to view this site', 403);
            }
        }
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

    public function getImages(int $siteId, string $type)
    {
        return $this->siteRepository->getImages($siteId, $type);
    }

    public function updateSiteInformation($siteId, array $data)
    {
        $this->siteRepository->updateSiteInformation($siteId, $data);
    }
}
