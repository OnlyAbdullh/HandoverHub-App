<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engine extends Model
{
    use HasFactory;

    protected $fillable = ['brand_id', 'capacity_id'];

    /**
     * Get the brand of this engine.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the capacity of this engine.
     */
    public function capacity()
    {
        return $this->belongsTo(Capacity::class);
    }

    /**
     * Get the generators using this engine.
     */
    public function generators()
    {
        return $this->hasMany(Generator::class);
    }

    /**
     * Get the parts associated with this engine.
     */
    public function parts()
    {
        return $this->belongsToMany(Part::class, 'engine_parts')
            ->withTimestamps();
    }
}
