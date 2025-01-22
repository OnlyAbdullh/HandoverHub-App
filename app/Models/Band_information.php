<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Band_information extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'band_type', 'rbs_1_type', 'rbs_2_type', 'du_1_type', 'du_2_type', 'ru_1_type', 'ru_2_type', 'gsm_900_remarks'];
}
