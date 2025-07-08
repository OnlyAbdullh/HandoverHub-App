<?php

namespace App\Imports;

use App\Models\MtnSite;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithUpserts
};

class MtnSitesImport implements ToModel, WithHeadingRow, WithUpserts
{
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
        return new MtnSite([
            'code' => trim($row['code']),
            'name' => trim($row['name']),
        ]);
    }

    public function uniqueBy()
    {
        return 'code';
    }
}
