<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ampere_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['site_id', 'capacity', 'time', 'cable_length', 'details'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
