<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generator_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'site_id',
        'generator_number',
        'gen_type_and_capacity',
        'gen_hour_meter',
        'gen_fuel_consumption',
        'internal_capacity',
        'internal_existing_fuel',
        'internal_cage',
        'external_capacity',
        'external_existing_fuel',
        'external_cage',
        'fuel_sensor_exiting',
        'fuel_sensor_working',
        'fuel_sensor_type',
        'ampere_to_owner',
        'circuit_breakers_quantity',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
