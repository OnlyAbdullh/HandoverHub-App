<?php

namespace App\Services;

use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class BrandService
{
    protected $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAllBrands(string $type = null): Collection
    {
        return $this->brandRepository->all($type);
    }

    public function createBrand(array $data): array
    {
        try {
            $brand = $this->brandRepository->create($data);

            return [
                'success' => true,
                'message' => 'Brand created successfully',
                'data' => $brand
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create brand: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function updateBrand(int $id, array $data): array
    {
        try {
            $brand = $this->brandRepository->find($id);

            if (!$brand) {
                return [
                    'success' => false,
                    'message' => 'Brand not found',
                    'data' => null
                ];
            }

            $updated = $this->brandRepository->update($id, $data);

            if ($updated) {
                $updatedBrand = $this->brandRepository->find($id);
                return [
                    'success' => true,
                    'message' => 'Brand updated successfully',
                    'data' => $updatedBrand
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to update brand',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update brand: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function deleteMultipleBrands(array $ids): array
    {
        try {
            $result = $this->brandRepository->deleteMany($ids);

            if ($result['deleted_count'] > 0) {
                return [
                    'success' => true,
                    'message' => 'Brands deleted successfully',
                    'not_found_ids' => $result['not_found_ids']
                ];
            }

            return [
                'success' => false,
                'message' => 'No brands were deleted',
                'not_found_ids' => $result['not_found_ids']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete brands: ' . $e->getMessage(),
                'not_found_ids' => []
            ];
        }
    }

}
