<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_general'];

    /**
     * Get the engines that use this part.
     */
    public function engines()
    {
        return $this->belongsToMany(Engine::class, 'engine_parts')
            ->withTimestamps();
    }

    /**
     * Get the reports where this part was replaced.
     */
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'replaced_parts')
            ->withPivot([
                'quantity',
                'notes',
                'is_faulty',
                'last_replacement_date',
                'current_work_hours',
                'last_replacement_hours'
            ])
            ->withTimestamps();
    }
}
