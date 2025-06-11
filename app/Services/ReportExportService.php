<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Illuminate\Support\Collection;

class ReportExportService
{
    protected ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Export reports to Excel file
     *
     * @param array $reportIds
     * @return string File path
     * @throws \Exception
     */
    public function exportReportsToExcel(array $reportIds): string
    {
        try {
            // Get reports with all details
            $reports = $this->getReportsForExport($reportIds);

            if ($reports->isEmpty()) {
                throw new \Exception('No reports found for the provided IDs');
            }

            // Transform data for Excel export
            $transformedData = $this->transformReportsForExcel($reports);

            // Generate Excel file
            $fileName = 'reports_export_' . uniqid() . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);

            // Ensure temp directory exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            Excel::store(new ReportsExport($transformedData), 'temp/' . $fileName);

            return $filePath;

        } catch (\Exception $e) {
            throw new \Exception('Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Get reports with all necessary details
     *
     * @param array $reportIds
     * @return Collection
     */
    protected function getReportsForExport(array $reportIds): Collection
    {
        return collect($reportIds)->map(function ($id) {
            return $this->reportRepository->findWithDetails($id);
        })->filter();
    }

    /**
     * Transform reports data for Excel format
     *
     * @param Collection $reports
     * @return array
     */
    protected function transformReportsForExcel(Collection $reports): array
    {
        return $reports->map(function ($report) {
            return [
                // Basic Report Info
                'report_id' => $report->id,
                'report_number' => $report->report_number,
                'visit_date' => $report->visit_date,
                'visit_time' => $report->visit_time,
                'visit_type' => $report->visit_type,
                'visit_reason' => $report->visit_reason,

                // Generator Info
                'generator_id' => $report->generator->id,
                'generator_brand' => $report->generator->brand->name ?? '',
                'initial_meter' => $report->generator->initial_meter,

                // Engine Info
                'engine_brand' => $report->generator->engine->brand->name ?? '',
                'engine_capacity' => $report->generator->engine->capacity->value ?? '',

                // Site Info
                'site_name' => $report->generator->mtn_site->name ?? '',
                'site_code' => $report->generator->mtn_site->code ?? '',

                // Technical Measurements
                'oil_pressure' => $report->oil_pressure,
                'temperature' => $report->temperature,
                'battery_voltage' => $report->battery_voltage,
                'oil_quantity' => $report->oil_quantity,
                'burned_oil_quantity' => $report->burned_oil_quantity,
                'frequency' => $report->frequency,
                'current_meter' => $report->current_meter,
                'ats_status' => $report->ats_status ? 'Active' : 'Inactive',

                // Voltage & Load
                'volt_l1' => $report->voltage_L1,
                'volt_l2' => $report->voltage_L2,
                'volt_l3' => $report->voltage_L3,
                'load_l1' => $report->load_L1,
                'load_l2' => $report->load_L2,
                'load_l3' => $report->load_L3,

                // Location
                'longitude' => $report->longitude,
                'latitude' => $report->latitude,

                // Previous Visit Info
                'last_meter' => $report->last_meter ,
                'last_routine_visit_date' => $report->last_routine_visit['visit_date'] ?? '',
                'last_routine_current_reading' => $report->last_routine_visit['current_reading'] ?? '',

                // Status & Notes
                'technical_status' => $report->technical_status,
                'technician_notes' => $this->formatNotes($report->technicianNotes),
                'completed_works' => $this->formatCompletedWorks($report->completedTasks),
                'replaced_parts' => $this->formatReplacedParts($report->replacedParts),
            ];
        })->toArray();
    }

    /**
     * Format technician notes for Excel
     *
     * @param $notes
     * @return string
     */
    protected function formatNotes($notes): string
    {
        if (!$notes || $notes->isEmpty()) {
            return '';
        }

        return $notes->pluck('note')->implode(' | ');
    }

    /**
     * Format completed works for Excel
     *
     * @param $tasks
     * @return string
     */
    protected function formatCompletedWorks($tasks): string
    {
        if (!$tasks || $tasks->isEmpty()) {
            return '';
        }

        return $tasks->pluck('description')->implode(' | ');
    }

    /**
     * Format replaced parts for Excel
     *
     * @param $parts
     * @return string
     */
    protected function formatReplacedParts($parts): string
    {
        if (!$parts || $parts->isEmpty()) {
            return '';
        }

        return $parts->map(function ($part) {
            $partInfo = $part->part->name . ' (Code: ' . $part->part->code . ')';
            $partInfo .= ' - Qty: ' . $part->quantity;
            if ($part->faulty_quantity > 0) {
                $partInfo .= ' - Faulty_Qty: ' . $part->faulty_quantity;
            }
            if ($part->note) {
                $partInfo .= ' - Note: ' . $part->note;
            }
            $partInfo .= ' - faulty: ' . ($part->is_faulty == 1 ? 'yes' : 'no');

            return $partInfo;
        })->implode(' | ');
    }
}
