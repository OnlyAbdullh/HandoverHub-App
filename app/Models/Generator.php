<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Generator extends Model
{
    use HasFactory;

    protected $fillable = [
        'engine_id',
        'brand_id',
        'mtn_site_id',
        'initial_meter',
    ];

    /**
     * Get the engine used by this generator.
     */
    public function engine()
    {
        return $this->belongsTo(Engine::class);
    }

    /**
     * Get the brand of this generator.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the site where this generator is located.
     */
    public function mtn_site(): BelongsTo
    {
        return $this->belongsTo(MtnSite::class, 'mtn_site_id');
    }


    /**
     * Get the reports for this generator.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
