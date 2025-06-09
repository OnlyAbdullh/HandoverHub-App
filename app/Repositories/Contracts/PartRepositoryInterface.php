<?php

namespace App\Repositories\Contracts;

interface PartRepositoryInterface
{
    public function all();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function deleteMany(array $ids);

    public function attachEngines($partId, array $engineIds);
    // public function syncEngines($partId, array $engineIds);
}
