<?php

namespace App\Repositories;

use App\Http\Resources\SiteResource;
use App\Models\Band_information;
use App\Models\Generator_information;
use App\Models\Image;
use App\Models\Rectifier_information;
use App\Models\Site;
use App\Models\Solar_wind_information;
use App\Models\Tower_information;
use App\Repositories\Contracts\SiteRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteRepository implements SiteRepositoryInterface
{
    protected $typeModelMapping = [
        'tower_images' => Tower_information::class,
        'solar_and_wind_batteries_images' => Solar_wind_information::class,
        'rectifier_images' => Rectifier_information::class,
        'rectifier_batteries_images' => Rectifier_information::class,
        'generator_images' => Generator_information::class,
        'rbs_images' => Band_information::class,
    ];

    protected $tcuTypeMap = [
        '2G' => 1,
        '3G' => 2,
        'LTE' => 4,
    ];

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
                    $this->storeImages($entity, $files[$key], $folder, $key);
                }
            }
        } else {
            // Otherwise use the single key
            if (isset($files[$imagesKey]) && !empty($files[$imagesKey])) {
                $entity = $entity ?: $site->{$relation}()->create([]);
                $folder = str_replace('_informations', '', $relation);
                $this->storeImages($entity, $files[$imagesKey], $folder, $imagesKey);
            }
        }
    }

    public function storeTcuInformation(Site $site, array $tcuData): void
    {
        $tcuTypesValue = $this->convertTcuTypes($tcuData['tcu_types'] ?? []);
        $tcuData['tcu_types'] = $tcuTypesValue;
        $site->tcu_informations()->create($tcuData);
    }

    public function getAllSites()
    {
        return Site::select('id', 'user_name', 'name', 'code', 'area', 'street', 'city')->get();
    }

    public function getSitesByUsername(string $username)
    {
        return Site::select('id','name', 'code', 'area', 'street', 'city')
            ->where('user_name', $username)
            ->get();
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

    public function getImages(int $siteId, string $type)
    {
        if (!array_key_exists($type, $this->typeModelMapping)) {
            throw new \InvalidArgumentException("Invalid image type: {$type}");
        }

        $modelClass = $this->typeModelMapping[$type];
        return Image::where('type', $type)
            ->whereHasMorph('imageable', [$modelClass], function ($query) use ($siteId) {
                $query->where('site_id', $siteId);
            })
            ->get();
    }

    public function updateSiteInformation($siteId, array $data)
    {
        $site = Site::findOrFail($siteId);

        if (isset($data['sites'])) {
            $site->update($data['sites']);
        }

        if (isset($data['tower_informations'])) {
            if ($site->tower_informations) {
                $site->tower_informations->update($data['tower_informations']);
            } else {
                $site->tower_informations()->create($data['tower_informations']);
            }
        }

        if (isset($data['band_informations'])) {
            foreach ($data['band_informations'] as $bandData) {
                if (!isset($bandData['band_type'])) {
                    throw new \Exception('band_type is missing from the request');
                }
                $bandType = $bandData['band_type'];
                if (!in_array($bandType, ['GSM 900', 'GSM 1800', '3G', 'LTE'])) {
                    throw new \Exception('no valid band_type is provided');
                }
                $bandRecord = $site->band_informations()->where('band_type', $bandType)->first();
                if ($bandRecord) {
                    $bandRecord->update($bandData);
                } else {
                    $site->band_informations()->create($bandData);
                }
            }
        }
        if (isset($data['generator_informations'])) {
            foreach ($data['generator_informations'] as $genData) {
                if (isset($genData['id'])) {
                    $generator = $site->generator_informations()->find($genData['id']);
                    if ($generator) {
                        $generator->update($genData);
                    }
                } else {
                    if ($site->generator_informations()->count() >= 2) {
                        throw new \Exception("There are already 2 generators for this site.");
                    }
                    $site->generator_informations()->create($genData);
                }
            }
        }

        if (isset($data['solar_wind_informations'])) {
            if ($site->solar_wind_informations) {
                $site->solar_wind_informations->update($data['solar_wind_informations']);
            } else {
                $site->solar_wind_informations()->create($data['solar_wind_informations']);
            }
        }

        if (isset($data['rectifier_informations'])) {
            if ($site->rectifier_informations) {
                $site->rectifier_informations->update($data['rectifier_informations']);
            } else {
                $site->rectifier_informations()->create($data['rectifier_informations']);
            }
        }

        if (isset($data['environment_informations'])) {
            if ($site->environment_informations) {
                $site->environment_informations->update($data['environment_informations']);
            } else {
                $site->environment_informations()->create($data['environment_informations']);
            }
        }

        if (isset($data['lvdp_informations'])) {
            if ($site->lvdp_informations) {
                $site->lvdp_informations->update($data['lvdp_informations']);
            } else {
                $site->lvdp_informations()->create($data['lvdp_informations']);
            }
        }

        if (isset($data['fiber_informations'])) {
            if ($site->fiber_informations) {
                $site->fiber_informations->update($data['fiber_informations']);
            } else {
                $site->fiber_informations()->create($data['fiber_informations']);
            }
        }

        if (isset($data['amperes_informations'])) {
            if ($site->amperes_informations) {
                $site->amperes_informations->update($data['amperes_informations']);
            } else {
                $site->amperes_informations()->create($data['amperes_informations']);
            }
        }

        if (isset($data['tcu_informations'])) {
            $tcuData = $data['tcu_informations'];
            $tcuData['tcu_types'] = $this->convertTcuTypes($tcuData['tcu_types'] ?? []);
            if ($site->tcu_informations) {
                $site->tcu_informations->update($tcuData);
            } else {
                $site->tcu_informations()->create($tcuData);
            }
        }
    }

    private function convertTcuTypes($types): int
    {
        if (!is_array($types)) {
            return (int)$types;
        }
        $tcuTypesValue = 0;
        foreach ($types as $type) {
            $tcuTypesValue |= $this->tcuTypeMap[trim($type)] ?? 0;
        }
        return $tcuTypesValue;
    }
}
