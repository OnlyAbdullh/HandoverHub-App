<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = [
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
    public function ampereInformation()
    {
        return $this->hasOne(Ampere_information::class);
    }
    public function tcuInformation()
    {
        return $this->hasOne(Tcu_information::class);
    }
    public function fiberInformation()
    {
        return $this->hasOne(Fiber_information::class);
    }
    public function bandInformation()
    {
        return $this->hasOne(Band_information::class);
    }

    public function rectifierInformation()
    {
        return $this->hasOne(Rectifier_information::class);
    }
    public function environmentInformation()
    {
        return $this->hasOne(Environment_information::class);
    }
    public function towerInformation()
    {
        return $this->hasOne(Tower_information::class);
    }
    public function solarWindInformation()
    {
        return $this->hasOne(Solar_wind_information::class);
    }
    public function generatorInformation()
    {
        return $this->hasOne(Generator_information::class);
    }
    public function lvdpInformation()
    {
        return $this->hasOne(Lvdp_information::class);
    }
}
