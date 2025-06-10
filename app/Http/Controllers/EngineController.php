<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEngineRequest;
use App\Http\Resources\EngineResource;
use App\Http\Resources\PartResource;
use App\Models\Engine;
use App\Services\EngineService;
use App\Exceptions\EngineException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'data' => EngineResource::collection($result['data']),
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
                'data' => new EngineResource($result['data']),
                'message' => $result['message'],
                'status' => $result['status'],
            ], $result['status']);

        } catch (EngineException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());

        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'brand_id' => 'sometimes|exists:brands,id',
            'capacity_id' => 'sometimes|exists:capacities,id',
        ]);

        if (empty($validated)) {
            return response()->json([
                'status' => 400,
                'message' => 'At least one field (brand_id or capacity_id) is required.',
                'data' => null
            ], 400);
        }

        $result = $this->engineService->update($id, $validated);

        return response()->json($result, $result['status']);
    }


    /**
     * Delete engine
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids');

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'data' => null,
                    'message' => 'Invalid or empty ID list',
                    'status' => 400
                ], 400);
            }

            $result = $this->engineService->deleteEngines($ids);

            return response()->json([
                'data' => null,
                'message' => $result['message'],
                'status' => $result['status'],
                'not_found_ids' => $result['not_found_ids'] ?? []
            ], $result['status']);

        } catch (EngineException $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ], $e->getCode());
        }
    }

    public function getPartsByEngine(Engine $engine): JsonResponse
    {
        $result = $this->engineService->getPartsByEngine($engine);

        if ($result['status'] === 200) {
            $result['data'] = PartResource::collection($result['data']);
        }
        return response()->json($result, $result['status']);
    }
}
