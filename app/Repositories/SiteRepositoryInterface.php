<?php

namespace App\Repositories;

use App\Models\Site;
use Illuminate\Http\UploadedFile;

interface SiteRepositoryInterface
{
    public function siteExists(string $code): bool;

    public function createSite(array $data): Site;

    public function storeImage($model, UploadedFile $image, string $path, string $type = 'original'): void;

    public function storeImages($model, array $images, string $path, string $type = 'original'): void;

    public function storeRelatedEntity(Site $site, string $relation, $imagesKey, array $data, array $files): void;

    public function storeTcuInformation(Site $site, array $tcuData): void;
    public function getAllSites();
    public function deleteSites(array $siteIds): int;
    public function getSiteDetails(int $siteId);
}
