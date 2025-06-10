<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

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
    public function getReportDetails(int $id): ?Report
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
            // Generate report number
            $reportNumber = $this->reportRepository->generateReportNumber($cityCode);
            $data['report']['report_number'] = $reportNumber;

            // Create report
            $report = $this->reportRepository->create($data['report']);

            // Add completed tasks
            if (isset($data['completed_task'])) {
                foreach ($data['completed_task'] as $task) {
                    $this->reportRepository->addCompletedTask($report->id, $task);
                }
            }

            // Add technician notes
            if (isset($data['technician_notes'])) {
                foreach ($data['technician_notes'] as $note) {
                    $this->reportRepository->addTechnicianNote($report->id, $note);
                }
            }

            // Add replaced parts
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
    public function deleteTechnicianNote(int $reportId, int $noteId): array
    {
        try {
            $deleted = $this->reportRepository->deleteTechnicianNote($reportId, $noteId);

            if (!$deleted) {
                return [
                    'status' => 404,
                    'message' => 'Note not found',
                    'data' => null
                ];
            }

            return [
                'status' => 200,
                'message' => 'Note deleted successfully',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to delete note: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Delete replaced part from report
     */
    public function deleteReplacedPart(int $reportId, int $partId): array
    {
        try {
            $deleted = $this->reportRepository->deleteReplacedPart($reportId, $partId);

            if (!$deleted) {
                return [
                    'status' => 404,
                    'message' => 'Part not found',
                    'data' => null
                ];
            }

            return [
                'status' => 200,
                'message' => 'Part deleted successfully',
                'data' => null
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => 'Failed to delete part: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
