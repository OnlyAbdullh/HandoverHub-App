<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedTask extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    /**
     * Get the reports that include this completed task.
     */
    public function reports()
    {
        return $this->belongsToMany(Report::class, 'report_tasks')
            ->withTimestamps();
    }
}
