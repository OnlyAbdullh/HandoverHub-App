<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tcu_information extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['site_id', 'tcu_types', 'remarks', 'tcu'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    protected $appends = ['tcu_types_array'];

    public function getTcuTypesArrayAttribute(): array
    {
        $tcuTypeMap = [
            '2G'  => 1,
            '3G'  => 2,
            'LTE' => 4,
        ];

        $result = [];
        foreach ($tcuTypeMap as $type => $bit) {
            if (($this->tcu_types & $bit) === $bit) {
                $result[] = $type;
            }
        }
        return $result;
    }
}
