<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MtnSite extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'longitude', 'latitude'];

    /**
     * Get the generators at this site.
     */
    public function generators()
    {
        return $this->hasMany(Generator::class);
    }

    /**
     * Get the reports generated for this site.
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
