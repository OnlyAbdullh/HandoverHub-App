<?php

namespace App\Repositories;

use App\Models\Part;
use App\Repositories\Contracts\PartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PartRepository implements PartRepositoryInterface
{
    protected $model;

    public function __construct(Part $model)
    {
        $this->model = $model;
    }

    public function all(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->with([
            'engines.brand',
            'engines.capacity'
        ])->paginate(20);
    }

    public function find($id): \Illuminate\Database\Eloquent\Builder|array|Collection|\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with([
            'engines.brand',
            'engines.capacity'
        ])->find($id);
    }

    public function create(array $data): Part
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $part = $this->model->find($id);
        if (!$part) {
            return false;
        }
        return $part->update($data);
    }

    public function deleteMany(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }


    public function attachEngines($partId, array $engineIds): void
    {
        $part = $this->model->find($partId);
        if ($part) {
            $part->engines()->attach($engineIds);
        }
    }

    /*  public function syncEngines($partId, array $engineIds): void
      {
          $part = $this->model->find($partId);
          if ($part) {
              $part->engines()->sync($engineIds);
          }
      }*/
    public function search(?string $name, ?string $code ): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Part::query();

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($code) {
            $query->where('code', 'like', "%{$code}%");
        }

        return $query->orderBy('name')
            ->paginate(20);
    }
}
