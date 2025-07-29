<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplacedPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'report_id',
        'quantity',
        'notes',
        'is_faulty',
        'faulty_quantity',
        'reason'
    ];

    protected $casts = [
        'is_faulty' => 'boolean',
        'quantity' => 'integer'
    ];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
