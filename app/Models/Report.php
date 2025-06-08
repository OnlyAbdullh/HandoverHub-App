<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'generator_id',
        'mtn_site_id',
        'visit_type',
        'report_number',
        'visit_date',
        'visit_time',
        'current_reading',
        'previous_reading',
        'link_status',
        'previous_visit_date',
        'oil_pressure',
        'temperature',
        'burned_oil_quantity',
        'battery_voltage',
        'frequency',
        'voltage_L1',
        'voltage_L2',
        'voltage_L3',
        'load_L1',
        'load_L2',
        'load_L3',
        'oil_quantity',
        'visit_reason',
        'technical_status'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
        'previous_visit_date' => 'date',
        'ats_status' => 'boolean',
        'current_reading' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'oil_pressure' => 'decimal:2',
        'temperature' => 'decimal:2',
        'burned_oil_quantity' => 'decimal:2',
        'battery_voltage' => 'decimal:2',
        'frequency' => 'decimal:2',
        'voltage_L1' => 'decimal:2',
        'voltage_L2' => 'decimal:2',
        'voltage_L3' => 'decimal:2',
        'load_L1' => 'decimal:2',
        'load_L2' => 'decimal:2',
        'load_L3' => 'decimal:2',
        'oil_quantity' => 'decimal:2'
    ];

    /**
     * Get the generator for this report.
     */
    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    /**
     * Get the site for this report.
     */
    public function mtn_site()
    {
        return $this->belongsTo(MtnSite::class, 'mtn_site_id');
    }

    /**
     * Get the completed tasks for this report.
     */
    public function completedTasks()
    {
        return $this->hasMany(CompletedTask::class);
    }

    /**
     * Get the replaced parts in this report.
     */
    public function replacedParts()
    {
        return $this->hasMany(ReplacedPart::class, 'report_id');
    }

    /**
     * Get the technician notes for this report.
     */
    public function technicianNotes()
    {
        return $this->hasMany(TechnicianNote::class);
    }
}
