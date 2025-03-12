<?php

namespace App\Repositories;

use App\Http\Resources\SiteResource;
use App\Models\Site;
use App\Repositories\SiteRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteRepository implements SiteRepositoryInterface
{
    public function siteExists(string $code): bool
    {
        return Site::where('code', $code)->exists();
    }

    public function createSite(array $data): Site
    {
        return Site::create($data);
    }

    public function storeImage($model, UploadedFile $image, string $path, string $type = 'original'): void
    {
        $model->images()->create([
            'image' => $image->store($path, 'public'),
            'type' => $type,
        ]);
    }

    public function storeImages($model, array $images, string $path, string $type = 'original'): void
    {
        foreach ($images as $image) {
            $this->storeImage($model, $image, $path, $type);
        }
    }

    public function storeRelatedEntity(Site $site, string $relation, $imagesKey, array $data, array $files): void
    {
        $entity = null;
        if (!empty($data)) {
            $entity = $site->{$relation}()->create($data);
        }

        // If imagesKey is an array, loop through each key-folder pair
        if (is_array($imagesKey)) {
            foreach ($imagesKey as $key => $folder) {
                if (isset($files[$key]) && !empty($files[$key])) {
                    $entity = $entity ?: $site->{$relation}()->create([]);
                    $this->storeImages($entity, $files[$key], $folder);
                }
            }
        } else {
            // Otherwise use the single key
            if (isset($files[$imagesKey]) && !empty($files[$imagesKey])) {
                $entity = $entity ?: $site->{$relation}()->create([]);
                $folder = str_replace('_informations', '', $relation);
                $this->storeImages($entity, $files[$imagesKey], $folder);
            }
        }
    }

    public function storeTcuInformation(Site $site, array $tcuData): void
    {
        if (isset($tcuData['tcu_types']) && is_array($tcuData['tcu_types'])) {
            $tcuTypeMap = [
                '2G' => 1,
                '3G' => 2,
                'LTE' => 4,
            ];
            $tcuTypesValue = 0;
            foreach ($tcuData['tcu_types'] as $type) {
                $tcuTypesValue |= $tcuTypeMap[trim($type)] ?? 0;
            }
            $tcuData['tcu_types'] = $tcuTypesValue;
        } else {
            $tcuData['tcu_types'] = 0;
        }
        $site->tcu_informations()->create($tcuData);
    }

    public function getAllSites()
    {
        return Site::select('name', 'code', 'area', 'street', 'city')->get();
    }

    public function deleteSites(array $siteIds): int
    {
        // 1) Collect all child-record IDs first, so we know what to look for in the images table.

        $generatorInfoIds = DB::table('generator_informations')
            ->whereIn('site_id', $siteIds)
            ->pluck('id')
            ->toArray();

        $bandInfoIds = DB::table('band_informations')
            ->whereIn('site_id', $siteIds)
            ->pluck('id')
            ->toArray();

        $towerInfoIds = DB::table('tower_informations')
            ->whereIn('site_id', $siteIds)
            ->pluck('id')
            ->toArray();

        $rectifierInfoIds = DB::table('rectifier_informations')
            ->whereIn('site_id', $siteIds)
            ->pluck('id')
            ->toArray();

        $solarWindInfoIds = DB::table('solar_wind_informations')
            ->whereIn('site_id', $siteIds)
            ->pluck('id')
            ->toArray();

        // 2) Build a single query to get images for Site + each child type.

        // We use a big "OR" chain to find images with (imageable_type = 'App\\Models\\Site' AND id in $siteIds)
        // or (imageable_type = 'App\\Models\\GeneratorInformation' AND id in $generatorInfoIds), etc.

        $imagesQuery = DB::table('images')
            ->where(function ($q) use ($siteIds) {
                $q->where('imageable_type', 'App\\Models\\Site')
                    ->whereIn('imageable_id', $siteIds);
            })
            ->orWhere(function ($q) use ($generatorInfoIds) {
                $q->where('imageable_type', 'App\\Models\\Generator_information')
                    ->whereIn('imageable_id', $generatorInfoIds);
            })
            ->orWhere(function ($q) use ($bandInfoIds) {
                $q->where('imageable_type', 'App\\Models\\Band_information')
                    ->whereIn('imageable_id', $bandInfoIds);
            })
            ->orWhere(function ($q) use ($towerInfoIds) {
                $q->where('imageable_type', 'App\\Models\\Tower_information')
                    ->whereIn('imageable_id', $towerInfoIds);
            })
            ->orWhere(function ($q) use ($rectifierInfoIds) {
                $q->where('imageable_type', 'App\\Models\\Rectifier_information')
                    ->whereIn('imageable_id', $rectifierInfoIds);
            })
            ->orWhere(function ($q) use ($solarWindInfoIds) {
                $q->where('imageable_type', 'App\\Models\\Solar_wind_information')
                    ->whereIn('imageable_id', $solarWindInfoIds);
            });

        // If you have more child types, keep chaining ->orWhere(...)

        // 3) Get all the file paths
        $allImages = $imagesQuery->pluck('image')->toArray();

        // 4) Delete those files in one go
        if (!empty($allImages)) {
            // Assuming 'image' column holds paths relative to storage/app/public,
            // e.g. "site/original/abc.jpg" or "generator/xyz.png"
            Storage::disk('public')->delete($allImages);
        }

        return Site::whereIn('id', $siteIds)->delete();
    }
    public function getSiteDetails(int $siteId)
    {
        $site = Site::with([
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
        ])->findOrFail($siteId);
        return new SiteResource($site);
    }
    public function getSiteImages(int $siteId, string $imageType)
    {
        $site = Site::findOrFail($siteId);

        return $site->images()->where('type', $imageType)->get();
    }
}
