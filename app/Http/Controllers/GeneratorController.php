<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignGeneratorsRequest;
use App\Http\Requests\StoreGeneratorRequest;
use App\Http\Requests\UpdateRequests\UpdateGeneratorRequest;
use App\Http\Resources\GeneratorResource;
use App\Imports\EnginesGeneratorsImport;
use App\Models\MtnSite;
use App\Services\GeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class GeneratorController extends Controller
{
    protected GeneratorService $generatorService;

    public function __construct(GeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * Display a listing of generators
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = $this->generatorService->getAllGenerators(false);

        return response()->json([
            'message' => 'All generators retrieved successfully',
            'total' => $result->total(),
            'count' => $result->count(),
            'current_page' => $result->currentPage(),
            'prev_page_url' => $result->previousPageUrl(),
            'next_page_url' => $result->nextPageUrl(),
            'data' => GeneratorResource::collection($result->items()),
        ]);

    }

    /**
     * GET /api/generators/unassigned
     * يُعيد فقط المولدات التي mtn_site_id فيها null
     */
    public function getUnassigned(): JsonResponse
    {
        $result = $this->generatorService->getAllGenerators(true);

        return response()->json($result, $result['status']);
    }

    /**
     * Store a newly created generator
     *
     * @param StoreGeneratorRequest $request
     * @return JsonResponse
     */
    public function store(StoreGeneratorRequest $request): JsonResponse
    {
        $result = $this->generatorService->createGenerator($request->validated());

        return response()->json($result, $result['status']);
    }

    /**
     * Display the specified generator
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->generatorService->getGeneratorDetails($id);

        return response()->json($result, $result['status']);
    }

    /**
     * Update the specified generator
     *
     * @param UpdateGeneratorRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateGeneratorRequest $request, int $id): JsonResponse
    {
        $result = $this->generatorService->updateGenerator($id, $request->validated());

        return response()->json($result, $result['status']);
    }

    /**
     * Remove the specified generator
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? null;

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'data' => null,
                'message' => 'Please provide a non-empty array of IDs.',
                'status' => 400
            ], 400);
        }

        $result = $this->generatorService->deleteGenerators($ids);

        return response()->json($result, $result['status']);
    }


    public function assignGeneratorsToSite(
        AssignGeneratorsRequest $request,
        MtnSite                 $site
    ): JsonResponse
    {
        $generatorIds = $request->getGeneratorIds();

        $result = $this->generatorService
            ->assignGeneratorsToSite($site, $generatorIds);

        return response()->json($result, $result['status']);
    }
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new EnginesGeneratorsImport, $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'تم استيراد المحركات والمولدات بنجاح.'
        ], 200);
    }
    public function search(Request $request): JsonResponse
    {
        $brandName = $request->query('brand');

        $paginated = $this->generatorService->searchByBrandName($brandName);

        return response()->json([
            'total'         => $paginated->total(),
            'count'         => $paginated->count(),
            'per_page'      => $paginated->perPage(),
            'current_page'  => $paginated->currentPage(),
            'prev_page_url' => $paginated->previousPageUrl(),
            'next_page_url' => $paginated->nextPageUrl(),
            'data'          => GeneratorResource::collection($paginated->items()),
        ]);
    }
}
