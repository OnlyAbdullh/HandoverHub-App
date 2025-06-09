<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateRequests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type');

            if ($type && !in_array($type, [Brand::TYPE_GENERATOR, Brand::TYPE_ENGINE])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid brand type. Must be either generator or engine.'
                ], 400);
            }

            $brands = $this->brandService->getAllBrands($type);

            return response()->json([
                'success' => true,
                'message' => 'Brands retrieved successfully',
                'data' => BrandResource::collection($brands)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $result = $this->brandService->createBrand($request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => new BrandResource($result['data'])
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $result = $this->brandService->updateBrand($id, $request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => new BrandResource($result['data'])
            ]);
        }

        $statusCode = $result['message'] === 'Brand not found' ? 404 : 400;

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], $statusCode);
    }

    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or empty ID list'
            ], 400);
        }

        $result = $this->brandService->deleteMultipleBrands($ids);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'not_found_ids' => $result['not_found_ids'] ?? []
        ], $result['success'] ? 200 : 400);
    }

}
