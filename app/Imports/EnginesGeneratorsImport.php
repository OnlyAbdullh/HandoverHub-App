<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Capacity;
use App\Models\Engine;
use App\Models\Generator;
use App\Models\MtnSite;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures
};

class EnginesGeneratorsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $siteCode = trim($row['code'] ?? '');
                $engineType = trim($row['engine_brand'] ?? '');
                $capacityValue = $row['capacity'] ?? null;
                $genType = trim($row['gen_brand'] ?? '');

                $site = MtnSite::where('code', $siteCode)->first();
                if (!$site) {
                    throw new \Exception("الموقع برمز «{$siteCode}» غير موجود.");
                }

                $engineBrand = Brand::firstOrCreate(
                    ['name' => $engineType, 'type' => 'engine']
                );

                $capacity = Capacity::firstOrCreate(
                    ['value' => $capacityValue]
                );

                $engine = Engine::firstOrCreate([
                    'brand_id' => $engineBrand->id,
                    'capacity_id' => $capacity->id,
                ]);

                $genBrandId = null;
                if ($genType !== '') {
                    $genBrand = Brand::firstOrCreate(
                        ['name' => $genType],
                        ['type' => 'generator']
                    );

                    $genBrandId = $genBrand->id;
                }
                \Log::info('mtn_site_id'. $site->id);
                \Log::info('engine_id'. $engine->id);
                \Log::info('brand_id'. $genBrandId);
                \Log::info('**************');
                Generator::create([
                    'mtn_site_id' => $site->id,
                    'engine_id' => $engine->id,
                    'brand_id' => $genBrandId,
                ]);

            }
        });
    }

    public function rules(): array
    {
        return [
            '*.code' => 'required|string|exists:mtn_sites,code',
            '*.engine_brand' => 'required|string',
            '*.capacity' => 'required|numeric',
            '*.gen_brand' => 'nullable|string',
        ];
    }

}
