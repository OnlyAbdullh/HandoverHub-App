<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lvdp_information extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'exiting', 'working', 'status', 'remarks'];
}
