<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capacity extends Model
{
    use HasFactory;

    protected $fillable = ['value'];


    protected $casts = [
        'value' => 'integer',
    ];

    /**
     * Get the engines with this capacity.
     */
    public function engines()
    {
        return $this->hasMany(Engine::class);
    }
}
