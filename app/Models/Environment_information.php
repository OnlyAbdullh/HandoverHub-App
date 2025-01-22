<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Environment_information extends Model
{
    use HasFactory;
    protected $fillable = [
        'power_control_serial_number',
        'ampere_consumption',
        'mini_phase',
        'three_phase',
        'power_control_ownership',
        'fan_quantity',
        'faulty_fan_quantity',
        'earthing_system',
        'air_conditioner_1_type',
        'air_conditioner_2_type',
        'stabilizer_quantity',
        'stabilizer_type',
        'fire_system',
        'exiting',
        'working',
        'remarks',
    ];
}
