<?php

namespace App\Repositories\Contracts;

interface CapacityRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function deleteMany(array $ids);
}
