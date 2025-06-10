<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Capacity;
use App\Models\Engine;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Exceptions\EngineException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

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
                'data' => $engines,
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

            //$engine->load(['brand:id,name', 'capacity:id,value']);

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

    public function update(int $id, array $data): array
    {
        try {
            $engine = $this->engineRepository->findById($id);

            if (!$engine) {
                return [
                    'status' => 404,
                    'message' => 'Engine not found.',
                    'data' => null,
                ];
            }

            $newBrandId = $data['brand_id'] ?? $engine->brand_id;
            $newCapacityId = $data['capacity_id'] ?? $engine->capacity_id;

            $duplicate = $this->engineRepository->existsByBrandAndCapacity($newBrandId, $newCapacityId, $id);
            if ($duplicate) {
                return [
                    'status' => 422,
                    'message' => 'An engine with the same brand and capacity already exists.',
                    'data' => null,
                ];
            }

            $updatedEngine = $this->engineRepository->update($engine, $data);
            $updatedEngine->load(['brand', 'capacity']);

            return [
                'status' => 200,
                'message' => 'Engine updated successfully.',
                'data' => $updatedEngine,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }


    /**
     * Delete engine
     */
    public function deleteEngines(array $ids): array
    {
        try {
            DB::beginTransaction();

            $result = $this->engineRepository->deleteMany($ids);

            if ($result['deleted_count'] === 0) {
                throw new EngineException('No engines were deleted');
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Engines deleted successfully',
                'status' => 200,
                'not_found_ids' => $result['not_found_ids']
            ];

        } catch (EngineException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting engines: ' . $e->getMessage());
            throw new EngineException('Failed to delete engines');
        }
    }


    public function getPartsByEngine(Engine $engine): array
    {
        try {
            $parts = $this->engineRepository->getPartsByEngine($engine);

            return [
                'data' => $parts,  // سنحوّله لاحقًا إلى Resource داخل الـ Controller إن أردت
                'message' => 'Parts retrieved successfully for engine ID ' . $engine->id,
                'status' => Response::HTTP_OK,
            ];
        } catch (\Exception $e) {
            Log::error('Error in EngineService@getPartsByEngine: ' . $e->getMessage());

            return [
                'data' => [],
                'message' => 'Server error while retrieving parts for engine.',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }
}
