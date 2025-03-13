<?php

use App\Http\Controllers\SiteInfrastructureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('site/store', [SiteInfrastructureController::class, 'storeAllData']);

Route::get('sites', [SiteInfrastructureController::class, 'index']);

Route::delete('sites/delete', [SiteInfrastructureController::class, 'deleteSites']);

Route::get('sites/{id}', [SiteInfrastructureController::class, 'showSite']);

Route::post('/sites/export', [SiteInfrastructureController::class, 'exportSelectedSites'])
    ->name('sites.export.selected');

Route::get('/site-images/{siteId}/{type}', [SiteInfrastructureController::class, 'getSiteImages']);

Route::get('/images/{siteId}/{type}', [SiteInfrastructureController::class, 'getImages']);
