<?php
namespace App\Repositories;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository implements BrandRepositoryInterface
{
    protected $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    public function all(string $type = null): Collection
    {
        $query = $this->model->orderBy('name', 'asc');

        if ($type) {
            $query->byType($type);
        }

        return $query->get();
    }

    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $brand = $this->find($id);

        if (!$brand) {
            return false;
        }

        return $brand->update($data);
    }

    public function delete(int $id): bool
    {
        $brand = $this->find($id);

        if (!$brand) {
            return false;
        }

        return $brand->delete();
    }
}
