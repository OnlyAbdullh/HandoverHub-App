<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function engines()
    {
        return $this->hasMany(Engine::class);
    }

    /**
     * Get the generators associated with this brand.
     */
    public function generators()
    {
        return $this->hasMany(Generator::class);
    }

    public static function importBatch(array $names, string $type): int
    {
        $unique = collect($names)
            ->map(fn($n) => strtolower(trim((string)$n))) // ← تحويل إلى lowercase مع trim
            ->filter()
            ->unique()
            ->values();

        if ($unique->isEmpty()) {
            return 0;
        }

        $now = now();
        $rows = $unique->map(fn($name) => [
            'name' => $name,
            'type' => $type,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        return self::upsert(
            $rows,
            ['name'],
            ['type', 'updated_at']
        );
    }

}
