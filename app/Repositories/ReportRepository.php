<?php

namespace App\Repositories;

use App\Models\Part;
use App\Models\Report;
use App\Models\CompletedTask;
use App\Models\TechnicianNote;
use App\Models\ReplacedPart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAware\Paginator;
use Illuminate\Support\Facades\DB;

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
    public function getAllReports(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->with(['mtn_site:id,name,code'])
            ->select('id', 'mtn_site_id', 'visit_type', 'visit_date')
            ->orderBy('visit_date', 'desc')
            ->paginate(20);
    }

    /**
     * Find report by ID with all relationships
     */

    public function findWithDetails(int $id)
    {
        $report = $this->model->with([
            'generator.brand:id,name',
            'generator.engine.brand:id,name',
            'generator.engine.capacity:id,value',
            'generator.mtn_site:id,name,code',
            'completedTasks:id,report_id,description',
            'technicianNotes:id,report_id,note',
            'replacedParts.part:id,name,code'
        ])->find($id);

        if (!$report) {
            return null;
        }

        $lastRoutineVisit = $this->model
            ->where('generator_id', $report->generator_id)
            ->where('id', '<', $id)
            ->where('visit_type', 'routine')
            ->orderBy('id', 'desc')
            ->select('visit_date', 'visit_time', 'current_reading')
            ->first();

        $report->last_routine_visit = $lastRoutineVisit ? [
            'visit_date' => $lastRoutineVisit->visit_date,
            'visit_time' => $lastRoutineVisit->visit_time,
            'current_reading' => $lastRoutineVisit->current_reading,
        ] : null;

        return $report;
    }

    public function getAllWithDetails(): Collection
    {
        $reports = $this->model
            ->with([
                'generator.brand:id,name',
                'generator.engine.brand:id,name',
                'generator.engine.capacity:id,value',
                'generator.mtn_site:id,name,code',
                'completedTasks:id,report_id,description',
                'technicianNotes:id,report_id,note',
                'replacedParts.part:id,name,code'
            ])
            ->get();

        return $reports->map(function ($report) {
            $last = $this->model
                ->where('generator_id', $report->generator_id)
                ->where('id', '<', $report->id)
                ->where('visit_type', 'routine')
                ->orderBy('id', 'desc')
                ->select('visit_date', 'visit_time', 'current_reading')
                ->first();

            $report->last_routine_visit = $last
                ? [
                    'visit_date' => $last->visit_date,
                    'visit_time' => $last->visit_time,
                    'current_reading' => $last->current_reading,
                ]
                : null;

            return $report;
        });
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
    public function deleteCompletedTasks(int $reportId, array $taskIds): int
    {
        return CompletedTask::where('report_id', $reportId)
            ->whereIn('id', $taskIds)
            ->delete();
    }


    /**
     * Delete technician note
     */
    public function deleteTechnicianNotes(int $reportId, array $noteIds): array
    {
        $toDelete = TechnicianNote::where('report_id', $reportId)
            ->whereIn('id', $noteIds)
            ->pluck('id')
            ->toArray();

        if (empty($toDelete)) {
            return [];
        }
        TechnicianNote::where('report_id', $reportId)
            ->whereIn('id', $toDelete)
            ->delete();

        return $toDelete;
    }

    /**
     * Delete replaced part
     */
    /**
     * Return a Collection of replaced_parts (part_id) for a given report.
     *
     * @param int $reportId
     * @return \Illuminate\Support\Collection
     */
    public function getReplacedPartsByReport(int $reportId)
    {
        return ReplacedPart::where('report_id', $reportId)
            ->get(['part_id']);
    }

    /**
     * Delete replaced_parts for given part_ids under a specific report.
     *
     * @param int $reportId
     * @param array $partIds
     * @return int    Number of records deleted
     */
    public function deleteReplacedParts(int $reportId, array $partIds): int
    {
        return ReplacedPart::where('report_id', $reportId)
            ->whereIn('part_id', $partIds)
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

    public function addTechnicianNote(int $reportId, string $note): TechnicianNote
    {
        $report = $this->model->findOrFail($reportId);

        return $report->technicianNotes()->create([
            'note' => $note,
        ]);
    }

    public function findReportOrFail(int $reportId): Report
    {
        return Report::findOrFail($reportId);
    }

    /**
     * التحقق من وجود Part برقم مُعيّن
     */
    public function partExists(int $partId): bool
    {
        return Part::where('id', $partId)->exists();
    }

    /**
     * التحقق من أن الجزء غير مُضاف مسبقاً في التقرير
     */
    public function partExistsInReport(Report $report, int $partId): bool
    {
        return $report
            ->replacedParts()
            ->where('part_id', $partId)
            ->exists();
    }

    /**
     * ربط Part بالتقرير بالبيانات الممررة
     */
    public function attachPart(Report $report, int $partId, array $pivotData): bool
    {
        $data = array_merge($pivotData, [
            'part_id' => $partId,
        ]);
        $report->replacedParts()->create($data);

        return true;
    }

}
