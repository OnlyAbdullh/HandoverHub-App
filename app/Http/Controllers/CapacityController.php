<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCapacityRequest;
use App\Http\Requests\UpdateRequests\UpdateCapacityRequest;
use App\Http\Resources\CapacityResource;
use App\Services\CapacityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CapacityController extends Controller
{
    protected $capacityService;

    public function __construct(CapacityService $capacityService)
    {
        $this->capacityService = $capacityService;
    }

    public function index(): JsonResponse
    {
        try {
            $capacities = $this->capacityService->getAllCapacities();

            return response()->json([
                'success' => true,
                'message' => 'Capacities retrieved successfully',
                'data' => CapacityResource::collection($capacities)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve capacities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreCapacityRequest $request): JsonResponse
    {
        $result = $this->capacityService->createCapacity($request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => new CapacityResource($result['data'])
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    public function update(UpdateCapacityRequest $request, int $id): JsonResponse
    {
        $result = $this->capacityService->updateCapacity($id, $request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => new CapacityResource($result['data'])
            ]);
        }

        $statusCode = $result['message'] === 'Capacity not found' ? 404 : 400;

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], $statusCode);
    }


    public function destroyList(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|distinct|exists:capacities,id',
        ]);

        $result = $this->capacityService->deleteCapacities($data['ids']);

        $status = $result['success'] ? 200 : 500;

        return response()->json($result, $status);
    }
}
