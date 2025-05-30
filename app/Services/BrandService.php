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

    public function deleteBrand(int $id): array
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

            $deleted = $this->brandRepository->delete($id);

            if ($deleted) {
                return [
                    'success' => true,
                    'message' => 'Brand deleted successfully',
                    'data' => null
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete brand',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete brand: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
