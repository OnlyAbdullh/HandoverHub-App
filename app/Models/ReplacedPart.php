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
        'last_replacement_date',
        'current_work_hours',
        'last_replacement_hours'
    ];

    protected $casts = [
        'is_faulty' => 'boolean',
        'last_replacement_date' => 'date',
        'current_work_hours' => 'decimal:2',
        'last_replacement_hours' => 'decimal:2',
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
