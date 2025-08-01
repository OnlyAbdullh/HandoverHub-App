<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedTask extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'description'];

    /**
     * Get the reports that include this completed task.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
