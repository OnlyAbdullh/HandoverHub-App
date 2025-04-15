<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id',
        'user_name',
        'name',
        'code',
        'governorate',
        'street',
        'area',
        'city',
        'type',
        'gsm1900',
        'gsm1800',
        '3g',
        'lte',
        'generator',
        'solar',
        'wind',
        'grid',
        'fence',
        'cabinet_number',
        'electricity_meter',
        'electricity_meter_reading',
        'generator_remark'
    ];

    public function amperes_informations()
    {
        return $this->hasOne(Ampere_information::class);
    }

    public function tcu_informations()
    {
        return $this->hasOne(Tcu_information::class);
    }

    public function fiber_informations()
    {
        return $this->hasOne(Fiber_information::class);
    }

    public function band_informations()
    {
        return $this->hasMany(Band_information::class);
    }

    public function rectifier_informations()
    {
        return $this->hasOne(Rectifier_information::class);
    }

    public function environment_informations()
    {
        return $this->hasOne(Environment_information::class);
    }

    public function tower_informations()
    {
        return $this->hasOne(Tower_information::class);
    }

    public function solar_wind_informations()
    {
        return $this->hasOne(Solar_wind_information::class);
    }

    public function generator_informations()
    {
        return $this->hasMany(Generator_information::class);
    }

    public function lvdp_informations()
    {
        return $this->hasOne(Lvdp_information::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
