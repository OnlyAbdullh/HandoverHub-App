<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEngineRequest;
use App\Services\EngineService;
use App\Exceptions\EngineException;
use Illuminate\Http\JsonResponse;

class EngineController extends Controller
{
    public function __construct(
        protected EngineService $engineService
    )
    {
    }

    /**
     * Get all engines
     */
    public function index(): JsonResponse
    {
        $result = $this->engineService->getAllEngines();

        return response()->json([
            'data' => $result['data'],
            'message' => $result['message'],
            'status' => $result['status']
        ], $result['status']);
    }

    /**
     * Create new engine
     */
    public function store(CreateEngineRequest $request): JsonResponse
    {
        try {
            $result = $this->engineService->createEngine($request->validated());

            return response()->json([
                'data' => $result['data'],
                'message' => $result['message'],
                'status' => $result['status']
            ], $result['status']);

        } catch (EngineException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());

        }
    }

    /**
     * Delete engine
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->engineService->deleteEngine($id);

            return response()->json([
                'data' => null,
                'message' => $result['message'],
                'status' => $result['status']
            ], $result['status']);

        } catch (EngineException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());

        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => 'Internal server error',
                'status' => 500
            ], 500);
        }
    }
}
