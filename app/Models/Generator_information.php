<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generator_information extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_id',
        'generator_number',
        'gen_type_and_capacity',
        'gen_hour_meter',
        'gen_fuel_consumption',
        'type',
        'capacity',
        'existing_fuel',
        'cage',
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
