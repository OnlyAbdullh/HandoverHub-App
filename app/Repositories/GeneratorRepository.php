<?php

namespace App\Repositories;

use App\Models\Generator;
use App\Repositories\Contracts\GeneratorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GeneratorRepository implements GeneratorRepositoryInterface
{
    protected Generator $model;

    public function __construct(Generator $model)
    {
        $this->model = $model;
    }

    /**
     * Get all generators with relationships
     *
     * @return Collection
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model
            ->with([
                'brand:id,name',
                'engine.brand:id,name',
                'engine.capacity:id,value',
                'mtn_site:id,name,code,longitude,latitude'
            ])
            ->get();
    }

    /**
     * Find generator by ID with relationships
     *
     * @param int $id
     */
    public function findWithRelations(int $id)
    {
        return $this->model->with([
            'brand:id,name',
            'engine.brand:id,name',
            'engine.capacity:id,value',
            'mtn_site:id,name,code,longitude,latitude'
        ])->find($id);

    }

    /**
     * Create new generator
     *
     * @param array $data
     * @return Generator
     */
    public function create(array $data): Generator
    {
        return $this->model->create($data);
    }

    /**
     * Update generator
     *
     * @param int $id
     * @param array $data
     * @return Generator
     */
    public function update(int $id, array $data): Generator
    {
        $generator = $this->model->findOrFail($id);
        $generator->update($data);

        return $generator->fresh();
    }

    /**
     * Delete generator
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $generator = $this->model->findOrFail($id);
        return $generator->delete();
    }

    /**
     * Check if generator exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
