<?php

namespace App\Imports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BrandsImport implements ToModel, WithStartRow
{
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = trim($row[0]);

        if (!$name) {
            return null;
        }

        return Brand::updateOrCreate(
            ['name' => $name],
            ['type' => $this->type]
        );
    }
}
