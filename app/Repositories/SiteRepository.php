<?php

namespace App\Repositories;

use App\Models\Site;
use App\Repositories\SiteRepositoryInterface;
use Illuminate\Http\UploadedFile;

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
}
