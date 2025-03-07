<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solar_wind_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['site_id', 'solar_type', 'solar_capacity', 'number_of_panels', 'number_of_modules', 'number_of_faulty_modules', 'number_of_batteries', 'battery_type', 'battery_status', 'wind_remarks', 'remarks'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
