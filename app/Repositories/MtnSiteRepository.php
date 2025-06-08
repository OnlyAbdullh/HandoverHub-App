<?php

namespace App\Repositories;

use App\Models\MtnSite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class MtnSiteRepository
{
    /**
     * The model instance.
     *
     * @var MtnSite
     */
    protected $model;

    /**
     * Constructor to inject dependencies
     *
     * @param MtnSite $model
     */
    public function __construct(MtnSite $model)
    {
        $this->model = $model;
    }

    /**
     * Get all MTN sites with filters
     *
     * @param array $filters
     */
    public function getAllWithFilters(array $filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        return $query->get();
    }

    /**
     * Find MTN site by ID
     *
     * @param int $id
     * @return MtnSite|null
     */
    public function findById(int $id): ?MtnSite
    {
        return $this->model->find($id);
    }

    /**
     * Create a new MTN site
     *
     * @param array $data
     * @return MtnSite
     */
    public function create(array $data): MtnSite
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing MTN site
     *
     * @param MtnSite $site
     * @param array $data
     * @return MtnSite
     */
    public function update(MtnSite $site, array $data): MtnSite
    {
        $site->update($data);
        return $site->fresh();
    }

    /**
     * Delete an MTN site
     *
     * @param MtnSite $site
     * @return bool
     */
    public function delete(MtnSite $site): bool
    {
        return $site->delete();
    }

    /**
     * Check if MTN site exists by code
     *
     * @param string $code
     * @param int|null $exceptId
     * @return bool
     */
    public function existsByCode(string $code, ?int $exceptId = null): bool
    {
        $query = $this->model->where('code', $code);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    public function getGenerators(int $siteId)
    {
        return MtnSite::with([
            'generators.engine.brand',
            'generators.engine.capacity',
            'generators.brand'
        ])
            ->findOrFail($siteId)
            ->generators;
    }

    public function getByIds(array $ids)
    {
        return MtnSite::whereIn('id', $ids)->get();
    }
}
