<?php

namespace App\Repositories\Contracts;

interface BrandRepositoryInterface
{
    public function all(string $type = null);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function deleteMany(array $ids);
}
