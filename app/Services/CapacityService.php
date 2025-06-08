<?php

namespace App\Services;

use App\Repositories\Contracts\CapacityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class CapacityService
{
    protected $capacityRepository;

    public function __construct(CapacityRepositoryInterface $capacityRepository)
    {
        $this->capacityRepository = $capacityRepository;
    }

    public function getAllCapacities(): Collection
    {
        return $this->capacityRepository->all();
    }

    public function createCapacity(array $data): array
    {
        try {
            $capacity = $this->capacityRepository->create($data);

            return [
                'success' => true,
                'message' => 'Capacity created successfully',
                'data' => $capacity
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create capacity: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function updateCapacity(int $id, array $data): array
    {
        try {
            $capacity = $this->capacityRepository->find($id);

            if (!$capacity) {
                return [
                    'success' => false,
                    'message' => 'Capacity not found',
                    'data' => null
                ];
            }

            $updated = $this->capacityRepository->update($id, $data);

            if ($updated) {
                $updatedCapacity = $this->capacityRepository->find($id);
                return [
                    'success' => true,
                    'message' => 'Capacity updated successfully',
                    'data' => $updatedCapacity
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to update capacity',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update capacity: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function deleteCapacities(array $ids): array
    {
        try {
            $deletedCount = $this->capacityRepository->deleteMany($ids);

            return [
                'success' => true,
                'message' => 'Capacities deleted successfully.',
                'deleted_count' => $deletedCount,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete capacities: ' . $e->getMessage(),
            ];
        }
    }

}
