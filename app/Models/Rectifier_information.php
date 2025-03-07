<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rectifier_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'site_id',
        'rectifier_1_type_and_voltage',
        'rectifier_2_type_and_voltage',
        'module_1_quantity',
        'module_2_quantity',
        'faulty_module_1_quantity',
        'faulty_module_2_quantity',
        'number_of_batteries',
        'battery_type',
        'batteries_cabinet_type',
        'cabinet_cage',
        'batteries_status',
        'remarks',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
