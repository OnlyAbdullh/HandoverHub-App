<?php

namespace App\Http\Controllers;

use App\Services\SiteService;
use Illuminate\Http\Request;

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
}
