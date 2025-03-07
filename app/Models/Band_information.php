<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Band_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['site_id', 'band_type', 'rbs_1_type', 'rbs_2_type', 'du_1_type', 'du_2_type', 'ru_1_type', 'ru_2_type', 'remarks'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
