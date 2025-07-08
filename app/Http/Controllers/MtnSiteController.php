<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMtnSiteRequest;
use App\Http\Requests\UpdateRequests\UpdateMtnSiteRequest;
use App\Http\Resources\GeneratorResource;
use App\Http\Resources\MtnSiteResource;
use App\Imports\MtnSitesImport;
use App\Models\MtnSite;
use App\Services\MtnSiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class MtnSiteController extends Controller
{
    protected $mtnSiteService;

    /**
     * Constructor to inject dependencies
     *
     * @param MtnSiteService $mtnSiteService
     */
    public function __construct(MtnSiteService $mtnSiteService)
    {
        $this->mtnSiteService = $mtnSiteService;
    }

    /**
     * Display a listing of the MTN sites with filtering capabilities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'code']);
        $sitesPaginated = $this->mtnSiteService->getAllSites($filters);

        $sitesData = MtnSiteResource::collection($sitesPaginated->items());

        return response()->json([
            'total' => $sitesPaginated->total(),
            'count' => $sitesPaginated->count(),
            'current_page' => $sitesPaginated->currentPage(),
            'prev_page_url' => $sitesPaginated->previousPageUrl(),
            'next_page_url' => $sitesPaginated->nextPageUrl(),
            'data' => $sitesData,
        ]);
    }

    /**
     * Store a newly created MTN site in storage.
     *
     * @param StoreMtnSiteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMtnSiteRequest $request)
    {
        $site = $this->mtnSiteService->createSite($request->validated());

        return response()->json([
            'data' => new MtnSiteResource($site),
            'message' => 'Site created successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified MTN site in storage.
     *
     * @param UpdateMtnSiteRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMtnSiteRequest $request, $id)
    {
        $site = $this->mtnSiteService->updateSite($id, $request->validated());

        return response()->json([
            'data' => new MtnSiteResource($site),
            'message' => 'Site updated successfully',
            'status' => 200
        ], 200);
    }

    /**
     * حذف مجموعة من مواقع MTN دفعة واحدة
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyBatch(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|distinct|exists:mtn_sites,id',
        ]);

        $result = $this->mtnSiteService->deleteSites($payload['ids']);

        return response()->json([
            'message' => 'MTN Sites deleted successfully',
            'deleted_count' => $result['deleted_count'],
        ], Response::HTTP_OK);
    }

    public function unlinkGenerators(Request $request, int $siteId): JsonResponse
    {
        $generatorIds = $request->input('generator_ids');

        if (!is_array($generatorIds) || empty($generatorIds)) {
            return response()->json([
                'status' => 422,
                'message' => 'Invalid or missing generator_ids array.',
                'data' => null
            ], 422);
        }

        $result = $this->mtnSiteService->unlinkGenerators($siteId, $generatorIds);

        return response()->json($result, $result['status']);
    }

    public function getGenerator(int $id): array
    {
        try {
            $generators = $this->mtnSiteService->getGeneratorsBySiteId($id);
            return [
                'data' => GeneratorResource::collection($generators),
                'message' => 'Generators retrieved successfully',
                'status' => 200
            ];

        } catch
        (\Exception $e) {

            \Log::error('Error fetching generators for site ID ' . $id . ': ' . $e->getMessage());

            return [
                'data' => [],
                'message' => 'Error retrieving generators',
                'status' => 500
            ];

        }
    }

    function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new MtnSitesImport, $request->file('file'));

        return response()->json(['message' => 'تم الاستيراد بنجاح']);
    }
}
