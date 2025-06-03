<?php

namespace App\Repositories;

use App\Models\Engine;
use App\Repositories\Contracts\EngineRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EngineRepository implements EngineRepositoryInterface
{
    public function __construct(
        protected Engine $model
    )
    {
    }

    /**
     * Get all engines with relationships
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model
            ->with(['brand', 'capacity'])
            ->get();
    }

    /**
     * Create new engine
     */
    public function create(array $data): Engine
    {
        return $this->model->create($data);
    }

    /**
     * Delete engine by ID
     */
    public function delete(int $id): bool
    {
        $engine = $this->model->find($id);

        if (!$engine) {
            return false;
        }

        return $engine->delete();
    }

    /**
     * Check if engine exists with given brand and capacity
     */
    public function existsByBrandAndCapacity(int $brandId, int $capacityId): bool
    {
        return $this->model
            ->where('brand_id', $brandId)
            ->where('capacity_id', $capacityId)
            ->exists();
    }

    /**
     * Find engine by ID (for internal use)
     */
    public function findById(int $id): ?Engine
    {
        return $this->model->find($id);
    }

    public function getPartsByEngine(Engine $engine): Collection
    {
        return $engine
            ->parts()
            ->get();
    }
}
