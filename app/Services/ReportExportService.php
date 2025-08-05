<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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
        $reports = $this->getReportsForExport($reportIds);

        $transformedData = $this->transformReportsForExcel($reports);

        $fileName = 'reports_export_' . uniqid() . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        Excel::store(new ReportsExport($transformedData), 'temp/' . $fileName);

        return $filePath;
    }

    /**
     * Get reports with all necessary details
     *
     * @param array $reportIds
     * @return Collection
     */
    protected function getReportsForExport(array $reportIds): Collection
    {
        return collect($reportIds)
            ->map(fn($id) => $this->reportRepository->findWithDetails($id))
            ->filter();
    }

    /**
     * Transform reports data for Excel format
     *
     * @param Collection $reports
     * @return array
     */

    protected function transformReportsForExcel(Collection $reports): array
    {
        $rows = [];

        foreach ($reports as $report) {
            $base = [
                'user_name'                    => $report->username ?? '',
                'report_id'                    => $report->id,
                'report_number'                => $report->report_number,
                'site_name'                    => $report->generator->mtn_site->name ?? '',
                'site_code'                    => $report->generator->mtn_site->code ?? '',
                'visit_date'                   => $report->visit_date,
                'engine_brand'                 => $report->generator->engine->brand->name ?? '',
                'engine_capacity'              => $report->generator->engine->capacity->value ?? '',
                'visit_type'                   => $report->visit_type,
                'visit_reason'                 => $report->visit_reason,
                'current_reading'              => $report->current_reading,
                'last_routine_visit_date'      => $report->last_routine_visit['visit_date'] ?? '',
                'last_routine_current_reading' => $report->last_routine_visit['current_reading'] ?? '',
                'technical_status'             => $report->technical_status,
                'technician_notes'             => $this->formatNotes($report->technicianNotes),
            ];

            if ($report->replacedParts->isEmpty()) {
                $rows[] = $base + [
                        'replaced_part'                     => '',
                        'qty'                               => '',
                        'faulty_qty'                        => '',
                        'last_replacement_date'             => '',
                        'generator_hours_at_last_replacement' => '',
                    ];
            } else {
                $first = $report->replacedParts->first();

                $rows[] = $base + [
                        'replaced_part'                     => "{$first->part->name} (Code: {$first->part->code})",
                        'qty'                               => $first->quantity,
                        'faulty_qty'                        => $first->faulty_quantity,
                        'last_replacement_date'             => $first->last_part_usage
                            ? Carbon::parse(Arr::get($first->last_part_usage, 'visit_date'))->format('Y-m-d')
                            : '',
                        'generator_hours_at_last_replacement' => Arr::get($first->last_part_usage, 'current_reading') ?? '',
                    ];

                $emptyBase = array_fill_keys(array_keys($base), '');

                foreach ($report->replacedParts->slice(1) as $rp) {
                    $rows[] = $emptyBase + [
                            'replaced_part'                     => "{$rp->part->name} (Code: {$rp->part->code})",
                            'qty'                               => $rp->quantity,
                            'faulty_qty'                        => $rp->faulty_quantity,
                            'last_replacement_date'             => $rp->last_part_usage
                                ? Carbon::parse(Arr::get($rp->last_part_usage, 'visit_date'))->format('Y-m-d')
                                : '',
                            'generator_hours_at_last_replacement' => Arr::get($rp->last_part_usage, 'current_reading') ?? '',
                        ];
                }
            }
        }

        return $rows;
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
