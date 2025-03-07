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
}
