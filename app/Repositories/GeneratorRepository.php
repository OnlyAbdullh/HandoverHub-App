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
     * @param bool $onlyUnassigned
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithRelations(bool $onlyUnassigned = false): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model
            ->with([
                'brand:id,name',
                'engine.brand:id,name',
                'engine.capacity:id,value',
                'mtn_site:id,name,code,longitude,latitude',
            ]);

        if ($onlyUnassigned) {
            $query->whereNull('mtn_site_id');
        }

        return $query->paginate(20);
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

    public function countUnassignedByIds(array $generatorIds): int
    {
        return $this->model
            ->whereIn('id', $generatorIds)
            ->whereNull('mtn_site_id')
            ->count();
    }

    /**
     * @param int[] $generatorIds
     * @param int $siteId
     * @return int
     */
    public function assignGeneratorsToSite(array $generatorIds, int $siteId): int
    {
        return $this->model
            ->whereIn('id', $generatorIds)
            ->whereNull('mtn_site_id')
            ->update(['mtn_site_id' => $siteId]);
    }
    public function searchByBrandName(?string $brandName): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Generator::with(
            ['brand:id,name',
            'engine.brand:id,name',
            'engine.capacity:id,value',
            'mtn_site:id,name,code,longitude,latitude']
        )
            ->whereHas('brand', function($q) {
                $q->where('type', 'generator');
            });

        if ($brandName) {
            $query->whereHas('brand', function($q) use ($brandName) {
                $q->where('name', 'like', "%{$brandName}%");
            });
        }

        return $query
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

}
