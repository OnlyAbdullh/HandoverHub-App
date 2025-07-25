<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdateRequests\UpdatePartRequest;
use App\Http\Resources\GeneratorResource;
use App\Http\Resources\PartResource;
use App\Imports\PartsImport;
use App\Services\PartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PartController extends Controller
{
    protected $partService;

    public function __construct(PartService $partService)
    {
        $this->partService = $partService;
    }

    public function index(): JsonResponse
    {
        try {
            $parts = $this->partService->getAllParts();
            $partsData = PartResource::collection($parts->items());

            return response()->json([
                'total' => $parts->total(),
                'count' => $parts->count(),
                'current_page' => $parts->currentPage(),
                'prev_page_url' => $parts->previousPageUrl(),
                'next_page_url' => $parts->nextPageUrl(),
                'data' => $partsData,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

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

    public function destroy(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids');

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'message' => 'Invalid or empty ID list',
                    'status' => 400
                ], 400);
            }

            $this->partService->deleteParts($ids);

            return response()->json([
                'message' => 'Parts deleted successfully',
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            $code = $e->getMessage() === 'Parts not found' ? 404 : 500;

            return response()->json([
                'message' => $e->getMessage(),
                'status' => $code
            ], $code);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new PartsImport, $request->file('file'));

        return response()->json(['message' => 'تم استيراد المواد بنجاح']);
    }
    public function search(Request $request)
    {
        $filters = $request->only(['name', 'code']);
        $sitesPaginated = $this->partService->search($filters);

        $sitesData =  PartResource::collection($sitesPaginated->items());

        return response()->json([
            'total' => $sitesPaginated->total(),
            'count' => $sitesPaginated->count(),
            'current_page' => $sitesPaginated->currentPage(),
            'prev_page_url' => $sitesPaginated->previousPageUrl(),
            'next_page_url' => $sitesPaginated->nextPageUrl(),
            'data' => $sitesData,
        ]);
    }
}
