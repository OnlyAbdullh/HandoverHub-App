<?php

namespace App\Repositories\Contracts;

use App\Models\Engine;
use Illuminate\Database\Eloquent\Collection;

interface EngineRepositoryInterface
{
    /**
     * Get all engines with relationships
     */
    public function getAllWithRelations(): Collection;

    /**
     * Create new engine
     */
    public function create(array $data): Engine;

    /**
     * Delete engine by ID
     */
    public function delete(int $id): bool;

    /**
     * Check if engine exists with given brand and capacity
     */
    public function existsByBrandAndCapacity(int $brandId, int $capacityId): bool;

    /**
     * Find engine by ID (for internal use)
     */
    public function findById(int $id): ?Engine;
}
