<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMtnSiteRequest;
use App\Http\Requests\UpdateRequests\UpdateMtnSiteRequest;
use App\Http\Resources\MtnSiteResource;
use App\Services\MtnSiteService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'code']);
        $sites = $this->mtnSiteService->getAllSites($filters);

        return MtnSiteResource::collection($sites);
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
     * Remove the specified MTN site from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->mtnSiteService->deleteSite($id);

        return response()->json([
            'message' => 'MTN Site deleted successfully'
        ], Response::HTTP_OK);
    }
    public function getGenerator()
    {

    }
}
