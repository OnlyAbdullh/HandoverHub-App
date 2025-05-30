<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type'
    ];

    protected $casts = [
        'type' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants for type enum
    const TYPE_GENERATOR = 'generator';
    const TYPE_ENGINE = 'engine';

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_GENERATOR => 'Generator',
            self::TYPE_ENGINE => 'Engine',
        ];
    }

    // Scopes
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeGenerators($query)
    {
        return $query->byType(self::TYPE_GENERATOR);
    }

    public function scopeEngines($query)
    {
        return $query->byType(self::TYPE_ENGINE);
    }

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
