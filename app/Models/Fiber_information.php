<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fiber_information extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'destination', 'remarks'];

    protected $hidden = ['created_at', 'updated_at'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
