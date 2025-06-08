<?php
namespace App\Repositories;

use App\Models\Report;
use App\Models\CompletedTask;
use App\Models\TechnicianNote;
use App\Models\ReplacedPart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAware\Paginator;

class ReportRepository
{
    protected $model;

    public function __construct(Report $model)
    {
        $this->model = $model;
    }

    /**
     * Get all reports with basic info
     */
    public function getAllReports(): Collection
    {
        return $this->model->with(['mtn_site:id,name,code'])
            ->select('id', 'mtn_site_id', 'visit_type', 'visit_date')
            ->orderBy('visit_date', 'desc')
            ->get();
    }

    /**
     * Find report by ID with all relationships
     */
    public function findWithDetails(int $id): ?Report
    {
        return $this->model->with([
            'generator.brand:id,name',
            'generator.engine.brand:id,name',
            'generator.engine.capacity:id,value',
            'generator.mtn_site:id,name,code',
            'completedTasks:id,report_id,description',
            'technicianNotes:id,report_id,note',
            'replacedParts.part:id,name,code'
        ])->find($id);
    }

    /**
     * Create new report
     */
    public function create(array $data): Report
    {
        return $this->model->create($data);
    }

    /**
     * Update report
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * Delete report
     */
    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }

    /**
     * Find report by ID
     */
    public function find(int $id): ?Report
    {
        return $this->model->find($id);
    }

    /**
     * Generate unique report number
     */
    public function generateReportNumber(string $cityCode): string
    {
        $month = now()->format('m');
        $year = now()->format('Y');

        $lastReport = $this->model->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('report_number', 'like', "$cityCode-$month-$year-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextId = $lastReport ? (int)explode('-', $lastReport->report_number)[3] + 1 : 1;

        return "$cityCode-$month-$year-$nextId";
    }

    /**
     * Add completed task to report
     */
    public function addCompletedTask(int $reportId, string $description): CompletedTask
    {
        return CompletedTask::create([
            'report_id' => $reportId,
            'description' => $description
        ]);
    }

    /**
     * Add technician note to report
     */
    public function addTechnicianNote(int $reportId, string $note): TechnicianNote
    {
        return TechnicianNote::create([
            'report_id' => $reportId,
            'note' => $note
        ]);
    }

    /**
     * Add replaced part to report
     */
    public function addReplacedPart(int $reportId, array $partData): ReplacedPart
    {
        $partData['report_id'] = $reportId;
        return ReplacedPart::create($partData);
    }

    /**
     * Delete completed task
     */
    public function deleteCompletedTask(int $reportId, int $taskId): bool
    {
        return CompletedTask::where('report_id', $reportId)
            ->where('id', $taskId)
            ->delete();
    }

    /**
     * Delete technician note
     */
    public function deleteTechnicianNote(int $reportId, int $noteId): bool
    {
        return TechnicianNote::where('report_id', $reportId)
            ->where('id', $noteId)
            ->delete();
    }

    /**
     * Delete replaced part
     */
    public function deleteReplacedPart(int $reportId, int $partId): bool
    {
        return ReplacedPart::where('report_id', $reportId)
            ->where('id', $partId)
            ->delete();
    }

    /**
     * Update completed tasks
     */
    public function updateCompletedTasks(int $reportId, array $tasks): void
    {
        foreach ($tasks as $task) {
            if (isset($task['id'])) {
                CompletedTask::where('id', $task['id'])
                    ->where('report_id', $reportId)
                    ->update(['description' => $task['description']]);
            } else {
                $this->addCompletedTask($reportId, $task['description']);
            }
        }
    }

    /**
     * Update technician notes
     */
    public function updateTechnicianNotes(int $reportId, array $notes): void
    {
        foreach ($notes as $note) {
            if (isset($note['id'])) {
                TechnicianNote::where('id', $note['id'])
                    ->where('report_id', $reportId)
                    ->update(['note' => $note['note']]);
            } else {
                $this->addTechnicianNote($reportId, $note['note']);
            }
        }
    }

    /**
     * Update replaced parts
     */
    public function updateReplacedParts(int $reportId, array $parts): void
    {
        // Delete existing parts for this report
        ReplacedPart::where('report_id', $reportId)->delete();

        // Add new parts
        foreach ($parts as $part) {
            $this->addReplacedPart($reportId, $part);
        }
    }
}
