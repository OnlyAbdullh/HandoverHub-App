<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Capacity;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Exceptions\EngineException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EngineService
{
    public function __construct(
        protected EngineRepositoryInterface $engineRepository
    )
    {
    }

    /**
     * Get all engines
     */
    public function getAllEngines(): array
    {
        try {
            $engines = $this->engineRepository->getAllWithRelations();

            return [
                'data' => $engines->toArray(),
                'message' => 'Engines retrieved successfully',
                'status' => 200
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching engines: ' . $e->getMessage());

            return [
                'data' => [],
                'message' => 'Failed to fetch engines',
                'status' => 500
            ];
        }
    }

    /**
     * Create new engine
     */
    public function createEngine(array $data): array
    {
        try {
            DB::beginTransaction();

            $brand = Brand::find($data['brand_id']);
            if (!$brand) {
                throw new EngineException('Brand not found');
            }

            if (strtolower($brand->type) !== 'engine') {
                throw new EngineException('Brand type must be "engine"');
            }

            $capacity = Capacity::find($data['capacity_id']);
            if (!$capacity) {
                throw new EngineException('Capacity not found');
            }

            if ($this->engineRepository->existsByBrandAndCapacity($data['brand_id'], $data['capacity_id'])) {
                throw new EngineException('Engine with this brand and capacity already exists');
            }

            $engine = $this->engineRepository->create($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Engine created successfully',
                'data' => $engine,
                'status' => 201
            ];

        } catch (EngineException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating engine: ' . $e->getMessage());
            throw new EngineException('Failed to create engine');
        }
    }

    /**
     * Delete engine
     */
    public function deleteEngine(int $id): array
    {
        try {
            DB::beginTransaction();

            $engine = $this->engineRepository->findById($id);
            if (!$engine) {
                throw new EngineException('Engine not found');
            }

            $deleted = $this->engineRepository->delete($id);

            if (!$deleted) {
                throw new EngineException('Failed to delete engine');
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Engine deleted successfully',
                'status' => 200
            ];

        } catch (EngineException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting engine: ' . $e->getMessage());
            throw new EngineException('Failed to delete engine');
        }
    }
}
