<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianNote extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'note'];

    /**
     * Get the report this note belongs to.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
