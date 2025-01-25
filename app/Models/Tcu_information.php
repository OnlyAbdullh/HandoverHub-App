<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tcu_information extends Model
{
    use HasFactory;
    protected $fillable = ['site_id', 'tcu_types', 'remarks','tcu'];
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
