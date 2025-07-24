<?php

namespace App\Repositories\Contracts;

use App\Models\Generator;
use Illuminate\Database\Eloquent\Collection;

interface GeneratorRepositoryInterface
{
    /**
     * Get all generators with relationships
     *
     * @return Collection
     */
    public function getAllWithRelations();

    /**
     * Find generator by ID with relationships
     *
     * @param int $id
     * @return Generator|null
     */
    public function findWithRelations(int $id);

    /**
     * Create new generator
     *
     * @param array $data
     * @return Generator
     */
    public function create(array $data): Generator;

    /**
     * Update generator
     *
     * @param int $id
     * @param array $data
     * @return Generator
     */
    public function update(int $id, array $data): Generator;

    /**
     * Delete generator
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Check if generator exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;

    public function searchByBrandName(?string $brandName);
}
