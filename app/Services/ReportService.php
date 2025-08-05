<?php

namespace App\Services;

use App\Models\TechnicianNote;
use App\Repositories\ReportRepository;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;
use InvalidArgumentException;

class ReportService
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Get all reports
     */
    public function getAllReports(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->reportRepository->getAllReports();
    }


    /**
     * Get report details
     */
    public function getReportDetails(int $id)
    {
        return $this->reportRepository->findWithDetails($id);
    }


    /**
     * Create new report
     */
    public function createReport(array $data, string $cityCode): array
    {
        DB::beginTransaction();
        try {
            $reportNumber = $this->reportRepository->generateReportNumber($cityCode);
            $data['report']['report_number'] = $reportNumber;
            $data['report']['username']=auth()->user()->username;
            $report = $this->reportRepository->create($data['report']);
            if (isset($data['completed_task'])) {
                foreach ($data['completed_task'] as $task) {
                    $this->reportRepository->addCompletedTask($report->id, $task);
                }
            }

            if (isset($data['technician_notes'])) {
                foreach ($data['technician_notes'] as $note) {
                    $this->reportRepository->addTechnicianNote($report->id, $note);
                }
            }

            if (isset($data['parts_used'])) {
                foreach ($data['parts_used'] as $part) {
                    $part['last_replacement_date'] = now()->toDateString();
                    $this->reportRepository->addReplacedPart($report->id, $part);
                }
            }

            DB::commit();

            return [
                'status' => 201,
                'message' => 'Report created successfully',
                'data' => ['id' => $report->id, 'report_number' => $reportNumber]
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => 500,
                'message' => 'Failed to create report: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update report
     */
    public function updateReport(array $data): array
    {
        DB::beginTransaction();
        try {
            $reportId = $data['report']['report_id'];

            // Check if report exists
            $report = $this->reportRepository->find($reportId);
            if (!$report) {
                return [
                    'status' => 404,
                    'message' => 'Report not found',
                    'data' => null
                ];
            }

            // Update report basic data
            unset($data['report']['report_id']);
            $this->reportRepository->update($reportId, $data['report']);

            // Update completed tasks
            if (isset($data['completed_task'])) {
                $this->reportRepository->updateCompletedTasks($reportId, $data['completed_task']);
            }

            // Update technician notes
            if (isset($data['technician_notes'])) {
                $this->reportRepository->updateTechnicianNotes($reportId, $data['technician_notes']);
            }

            // Update replaced parts
            if (isset($data['parts_used'])) {
                $this->reportRepository->updateReplacedParts($reportId, $data['parts_used']);
            }

            DB::commit();

            return [
                'status' => 200,
                'message' => 'Report updated successfully',
                'data' => ['id' => $reportId]
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => 500,
                'message' => 'Failed to update report: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Delete report
     */
    public function deleteReport(int $id): array
    {
        try {
            $report = $this->reportRepository->find($id);
            if (!$report) {
                return [
                    'status' => 404,
                    'message' => 'Report not found',
                    'data' => null
                ];
            }

            $this->reportRepository->delete($id);

            return [
                'status' => 200,
                'message' => 'Report deleted successfully',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to delete report: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Add completed task to report
     */
    public function addCompletedTask(int $reportId, string $description): array
    {
        try {
            $report = $this->reportRepository->find($reportId);
            if (!$report) {
                return [
                    'status' => 404,
                    'message' => 'Report not found',
                    'data' => null
                ];
            }

            $task = $this->reportRepository->addCompletedTask($reportId, $description);

            return [
                'status' => 201,
                'message' => 'Task added successfully',
                'data' => ['id' => $task->id]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to add task: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Delete completed task from report
     */
    public function deleteCompletedTasks(int $reportId, array $taskIds): array
    {
        try {
            $deletedCount = $this->reportRepository->deleteCompletedTasks($reportId, $taskIds);

            if ($deletedCount === 0) {
                return [
                    'status' => 404,
                    'message' => 'No matching tasks found to delete.',
                    'data' => null
                ];
            }

            return [
                'status' => 200,
                'message' => "Deleted $deletedCount task(s) successfully.",
                'data' => ['deleted_count' => $deletedCount]
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to delete tasks: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }


    /**
     * Delete technician note from report
     */
    public function deleteTechnicianNotes(int $reportId, array $noteIds): array
    {
        try {
            $deletedIds = $this->reportRepository->deleteTechnicianNotes($reportId, $noteIds);

            if (empty($deletedIds)) {
                return [
                    'status' => 404,
                    'message' => 'No matching notes found to delete.',
                    'data' => [],
                ];
            }

            return [
                'status' => 200,
                'message' => 'Notes deleted successfully.',
                'data' => $deletedIds,  // list of IDs actually deleted
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to delete notes: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Delete replaced part from report
     */

    /**
     * Delete only the replaced parts that actually exist under the report.
     *
     * @param  int    $reportId
     * @param  array  $partIds
     * @return array
     */
    public function deleteReplacedParts(int $reportId, array $partIds): array
    {
        DB::beginTransaction();
        try {
            $existing = $this->reportRepository
                ->getReplacedPartsByReport($reportId)
                ->pluck('part_id')
                ->toArray();

            $toDelete = array_values(array_intersect($partIds, $existing));

            if (empty($toDelete)) {
                return [
                    'status'  => 200,
                    'message' => 'No matching parts found; nothing to delete.',
                    'data'    => ['deleted_count' => 0],
                ];
            }

            $deletedCount = $this->reportRepository
                ->deleteReplacedParts($reportId, $toDelete);

            DB::commit();

            $ignored = array_diff($partIds, $toDelete);
            $msg = "{$deletedCount} part(s) deleted successfully.";
            if (!empty($ignored)) {
                $msg .= ' Ignored IDs: ' . implode(', ', $ignored) . '.';
            }

            return [
                'status'  => 200,
                'message' => $msg,
                'data'    => [
                    'deleted_count' => $deletedCount,
                    'ignored_ids'   => array_values($ignored),
                ],
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status'  => 500,
                'message' => 'Failed to delete parts: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function addTechnicianNote(int $reportId, string $note)
    {
        if (empty(trim($note ?? ''))) {
            throw new InvalidArgumentException('Note content cannot be empty');
        }
        $this->reportRepository->addTechnicianNote($reportId, $note);
    }

    /**
     * إضافة مادة/قطعة لتقرير
     */
    public function addPartToReport(int $reportId, int $partId, array $data = []): bool
    {
        $report = $this->reportRepository->findReportOrFail($reportId);

        if (!$this->reportRepository->partExists($partId)) {
            throw new ModelNotFoundException("Part #{$partId} not found");
        }

        if ($this->reportRepository->partExistsInReport($report, $partId)) {
            throw new InvalidArgumentException("Part #{$partId} already added to Report #{$reportId}");
        }
        $quantity = $data['quantity'] ?? 1;
        if (!is_numeric($quantity) || $quantity < 1) {
            throw new InvalidArgumentException("Quantity must be a positive number");
        }

        $pivotData = [
            'quantity' => (double)$quantity,
            'faulty_quantity' => isset($data['faulty_quantity'])
                ? (double)$data['faulty_quantity']
                : 0,
            'notes' => $data['notes'] ?? null,
            'is_faulty' => isset($data['is_faulty']) && $data['is_faulty'],
        ];

        return $this->reportRepository->attachPart($report, $partId, $pivotData);
    }
    public function getReportIdsByDateRange(string $startDate, string $endDate): array
    {
        return $this->reportRepository
            ->getReportsForExport($startDate, $endDate)
            ->toArray();
    }

}
