<?php
// app/Http/Controllers/API/ReportController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportReportsRequest;
use App\Http\Requests\ReportPartRequest;
use App\Http\Resources\ReportResource;
use App\Http\Resources\ReportShowResource;
use App\Services\ReportExportService;
use App\Services\ReportService;
use App\Http\Requests\StoreReportRequest;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService, ReportExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    /**
     * Get all reports
     */

    public function index(): JsonResponse
    {
        $paginator = $this->reportService->getAllReports();

        return response()->json([
            'total' => $paginator->total(),
            'count' => $paginator->count(),
            'current_page' => $paginator->currentPage(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'data' => ReportResource::collection($paginator),
        ]);
    }

    /**
     * Get report details
     */
    public function show(int $id): JsonResponse
    {
        $report = $this->reportService->getReportDetails($id);

        if (!$report) {
            return response()->json([
                'status' => 404,
                'message' => 'Report not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Report details retrieved successfully',
            'data' => new ReportShowResource($report)
        ], 200);
    }

    /**
     * Create new report
     */
    public function store(StoreReportRequest $request): JsonResponse
    {
        $cityCode = $request->input('city_code', 'DEFAULT');
        $result = $this->reportService->createReport($request->validated(), $cityCode);
        return response()->json($result, $result['status']);
    }

    /**
     * Update report
     */
    public function update(StoreReportRequest $request): JsonResponse
    {
        $result = $this->reportService->updateReport($request->validated());
        return response()->json($result, $result['status']);
    }

    /**
     * Delete report
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->reportService->deleteReport($id);
        return response()->json($result, $result['status']);
    }

    /**
     * Add completed task to report
     */
    public function addTask(Request $request, int $reportId): JsonResponse
    {
        $result = $this->reportService->addCompletedTask(
            $reportId,
            $request->input('description')
        );
        return response()->json($result, $result['status']);
    }

    /**
     * Delete completed task from report
     */
    public function deleteTasks(Request $request, int $reportId): JsonResponse
    {
        $taskIds = $request->input('task_ids');

        if (!is_array($taskIds) || empty($taskIds)) {
            return response()->json([
                'status' => 422,
                'message' => 'Invalid or missing task_ids array.',
                'data' => null
            ], 422);
        }

        $result = $this->reportService->deleteCompletedTasks($reportId, $taskIds);

        return response()->json($result, $result['status']);
    }


    /**
     * Delete technician note from report
     */
    public function deleteNotes(Request $request, int $reportId): JsonResponse
    {
        $request->validate([
            'note_ids' => 'required|array',
            'note_ids.*' => 'integer|distinct',
        ]);

        $result = $this->reportService->deleteTechnicianNotes($reportId, $request->input('note_ids'));

        return response()->json($result, $result['status']);
    }

    /**
     * Delete replaced part from report
     */
    public function deleteParts(int $reportId, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'part_ids' => 'required|array|min:1',
            'part_ids.*' => 'integer',
        ]);

        $result = $this->reportService->deleteReplacedParts(
            $reportId,
            $validated['part_ids']
        );

        return response()->json(
            ['message' => $result['message'], 'data' => $result['data']],
            $result['status']
        );
    }

    public function addTechnicianNote(int $reportId, Request $request): JsonResponse
    {
        try {
            $this->reportService->addTechnicianNote(
                $reportId,
                $request->input('note')
            );

            return response()->json([
                'success' => true,
                'message' => 'Technician note added successfully',
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 400);
        }
    }

    /**
     * إضافة مادة/قطعة لتقرير
     * POST /api/reports/{reportId}/add-part
     */
    public function addPart(int $reportId, ReportPartRequest $request): JsonResponse
    {
        try {
            $this->reportService->addPartToReport(
                $reportId,
                $request->part_id,
                $request->only(['quantity', 'notes', 'is_faulty', 'faulty_quantity'])
            );

            return response()->json([
                'success' => true,
                'message' => 'Part added to report successfully'
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 400);
        }
    }


    public function exportReports(ExportReportsRequest $request): JsonResponse|BinaryFileResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate   = $request->input('end_date');

            $reportIds = $this->reportService->getReportIdsByDateRange($startDate, $endDate);

            if (empty($reportIds)) {
                $filePath = $this->exportService->exportReportsToExcel([]);
                $fileName = "reports_{$startDate}_to_{$endDate}_empty_" . now()->format('Y-m-d_H-i-s') . '.xlsx';

                return response()->download($filePath, $fileName)->deleteFileAfterSend();
            }

            $filePath = $this->exportService->exportReportsToExcel($reportIds);
            $fileName = "reports_{$startDate}_to_{$endDate}_" . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return response()->download($filePath, $fileName)->deleteFileAfterSend();

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تصدير التقارير',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
