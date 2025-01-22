<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tower_information extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'mast', 'tower', 'monopole', 'mast_number', 'mast_status', 'tower_number', 'tower_status', 'beacon_status', 'monopole_number', 'monopole_status', 'mast_1_height', 'mast_2_height', 'mast_3_height', 'tower_1_height', 'tower_2_height', 'monopole_height', 'remarks'];
}
