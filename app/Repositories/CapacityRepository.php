<?php

namespace App\Repositories;

use App\Models\Capacity;
use App\Repositories\Contracts\CapacityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CapacityRepository implements CapacityRepositoryInterface
{
    protected $model;

    public function __construct(Capacity $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('value', 'asc')->get();
    }

    public function find(int $id): ?Capacity
    {
        return $this->model->find($id);
    }

    public function create(array $data): Capacity
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $capacity = $this->find($id);

        if (!$capacity) {
            return false;
        }

        return $capacity->update($data);
    }

    public function deleteMany(array $ids): int
    {
        return Capacity::whereIn('id', $ids)->delete();
    }
}
