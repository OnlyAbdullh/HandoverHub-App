<?php

namespace App\Repositories;

use App\Models\CompletedTask;
use App\Repositories\Contracts\CompletedTaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CompletedTaskRepository implements CompletedTaskRepositoryInterface
{
    protected $model;

    public function __construct(CompletedTask $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->get();
    }

    public function find(int $id): ?CompletedTask
    {
        return $this->model->find($id);
    }

    public function create(array $data): CompletedTask
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $task = $this->find($id);

        if (!$task) {
            return false;
        }

        return $task->update($data);
    }

    public function delete(int $id): bool
    {
        $task = $this->find($id);

        if (!$task) {
            return false;
        }

        return $task->delete();
    }
}
