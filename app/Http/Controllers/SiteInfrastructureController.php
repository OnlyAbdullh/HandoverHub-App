<?php

namespace App\Http\Controllers;

use App\Exports\SitesExport;
use App\Http\Resources\ImageResource;
use App\Services\SiteService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SiteInfrastructureController extends Controller
{
    protected $siteService;

    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    public function storeAllData(Request $request)
    {
        return $this->siteService->storeAllData($request);
    }

    public function index()
    {
        $sites = $this->siteService->getAllSites();
        return response()->json($sites);
    }

    public function deleteSites(Request $request)
    {
        $siteIds = $request->input('ids', []);
        if (empty($siteIds)) {
            return response()->json(['message' => 'No site IDs provided'], 400);
        }

        try {
            $deletedCount = $this->siteService->deleteSites($siteIds);
            return response()->json([
                'message' => "$deletedCount site(s) deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showSite(int $id)
    {
        try {
            $site = $this->siteService->getSiteDetails($id);
            return response()->json($site);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function exportSelectedSites(Request $request)
    {
        $siteIds = $request->input('site_ids', []);

        return Excel::download(new SitesExport($siteIds), 'sites.xlsx');
    }

    public function getSiteImages($siteId, $type)
    {
        try {
            $images = $this->siteService->getSiteImages($siteId, $type);
            return response()->json([
                'data' => ImageResource::collection($images)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
