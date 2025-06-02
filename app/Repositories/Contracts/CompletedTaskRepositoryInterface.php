<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\CompletedTask;

interface CompletedTaskRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?CompletedTask;

    public function create(array $data): CompletedTask;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
