<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompletedTaskRequest;
use App\Http\Resources\CompletedTaskResource;
use App\Services\CompletedTaskService;
use Illuminate\Http\JsonResponse;

class CompletedTaskController extends Controller
{
    protected $completedTaskService;

    public function __construct(CompletedTaskService $completedTaskService)
    {
        $this->completedTaskService = $completedTaskService;
    }

    public function index(): JsonResponse
    {
        try {
            $tasks = $this->completedTaskService->getAllTasks();

            return response()->json([
                'success' => true,
                'message' => 'Completed Tasks retrieved successfully',
                'data'    => CompletedTaskResource::collection($tasks),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Completed Tasks',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CompletedTaskRequest $request): JsonResponse
    {
        $result = $this->completedTaskService->createTask($request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data'    => new CompletedTaskResource($result['data']),
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 400);
    }

    public function update(CompletedTaskRequest $request, int $id): JsonResponse
    {
        $result = $this->completedTaskService->updateTask($id, $request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data'    => new CompletedTaskResource($result['data']),
            ]);
        }

        $statusCode = $result['message'] === 'Completed Task not found' ? 404 : 400;

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], $statusCode);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->completedTaskService->deleteTask($id);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        }

        $statusCode = $result['message'] === 'Completed Task not found' ? 404 : 400;

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], $statusCode);
    }
}
