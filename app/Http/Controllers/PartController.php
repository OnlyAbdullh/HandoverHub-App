<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdateRequests\UpdatePartRequest;
use App\Http\Resources\PartResource;
use App\Services\PartService;
use Exception;
use Illuminate\Http\JsonResponse;

class PartController extends Controller
{
    protected $partService;

    public function __construct(PartService $partService)
    {
        $this->partService = $partService;
    }

    /**
     * عرض جميع القطع
     */
    public function index(): JsonResponse
    {
        try {
            $parts = $this->partService->getAllParts();

            return response()->json([
                'data' => PartResource::collection($parts),
                'message' => 'Engines retrieved successfully',
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * إنشاء قطعة جديدة
     */
    public function store(StorePartRequest $request): JsonResponse
    {
        try {
            $part = $this->partService->createPart($request->validated());

            return response()->json([
                'data' => new PartResource($part),
                'message' => 'part created successfully',
                'status' => 201
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * تحديث قطعة موجودة
     */
    public function update(UpdatePartRequest $request, $id): JsonResponse
    {
        try {
            $part = $this->partService->updatePart($id, $request->validated());

            return response()->json([
                'data' => new PartResource($part),
                'message' => 'Engine updated successfully',
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => $e->getMessage() === 'part is not exist' ? 404 : 500
            ], $e->getMessage() === 'part is not exist' ? 404 : 500);
        }
    }

    /**
     * حذف قطعة
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->partService->deletePart($id);

            return response()->json([
                'message' => 'part deleted successfully',
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => $e->getMessage() === 'part is not exist' ? 404 : 500
            ], $e->getMessage() === 'part is not exist' ? 404 : 500);
        }
    }
}
