<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandImportController;
use App\Http\Controllers\CapacityController;
use App\Http\Controllers\CompletedTaskController;
use App\Http\Controllers\EngineController;
use App\Http\Controllers\GeneratorController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiteInfrastructureController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MtnSiteController;

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
Route::middleware('auth:sanctum')->group(function () {

    Route::post('site/store', [SiteInfrastructureController::class, 'storeAllData'])
        ->middleware('permission:site.create');

    Route::get('sites', [SiteInfrastructureController::class, 'index'])
        ->middleware('permission:site.get');

    Route::get('site-images/{siteId}/{type}', [SiteInfrastructureController::class, 'getSiteImages'])
        ->middleware('permission:site.images');

    Route::get('images/{siteId}/{type}', [SiteInfrastructureController::class, 'getImages'])
        ->middleware('permission:site.images');

    Route::get('sites/{id}', [SiteInfrastructureController::class, 'showSite'])
        ->middleware('permission:site.view');

    Route::put('sites/{id}', [SiteInfrastructureController::class, 'update'])
        ->middleware('permission:site.update');

    Route::delete('sites/delete', [SiteInfrastructureController::class, 'deleteSites'])
        ->middleware('permission:site.delete');

    Route::post('sites/export', [SiteInfrastructureController::class, 'exportSelectedSites'])
        ->middleware('permission:site.export')
        ->name('sites.export.selected');

    Route::post('admin/generate-user', [AuthController::class, 'register'])
        ->middleware('permission:user.create');

    Route::get('users', [UserController::class, 'getUsers'])
        ->middleware('permission:user.view');

    Route::put('users-edit', [UserController::class, 'updateUser'])
        ->middleware('permission:user.update');

    Route::delete('users-delete', [UserController::class, 'deleteUsers'])
        ->middleware('permission:user.delete');

    Route::get('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);


Route::post('brands/import', [BrandImportController::class, 'import']);
Route::post('brands', [BrandImportController::class, 'store']);

Route::apiResource('mtn-sites', MtnSiteController::class);
Route::post('mtn-sites/search', [MtnSiteController::class, 'index']);
Route::get('mtn-sites/generators/{id}', [MtnSiteController::class, 'getGenerator']);
Route::post('/mtn-sites/{site}/assign-generators', [GeneratorController::class, 'assignGeneratorsToSite']);

Route::prefix('capacities')->group(function () {
    Route::get('/', [CapacityController::class, 'index']);
    Route::post('/', [CapacityController::class, 'store']);
    Route::put('/{id}', [CapacityController::class, 'update']);
    Route::delete('/{id}', [CapacityController::class, 'destroy']);
});

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::post('/', [BrandController::class, 'store']);
    Route::put('/{brand}', [BrandController::class, 'update']);
    Route::delete('/{brand}', [BrandController::class, 'destroy']);
});

Route::apiResource('engines', EngineController::class)->only([
    'index', 'store', 'destroy'
]);
Route::get('/engines/parts/{engine}', [EngineController::class, 'getPartsByEngine'])
    ->name('engines.parts')
    ->whereNumber('engine');

Route::prefix('parts')->group(function () {
    Route::get('/', [PartController::class, 'index']);
    Route::post('/', [PartController::class, 'store']);
    Route::put('/{id}', [PartController::class, 'update']);
    Route::delete('/{id}', [PartController::class, 'destroy']);
});

Route::prefix('generators')->group(function () {
    Route::get('/unassigned', [GeneratorController::class, 'getUnassigned']);
    Route::get('/', [GeneratorController::class, 'index']);
    Route::post('/', [GeneratorController::class, 'store']);
    Route::get('/{id}', [GeneratorController::class, 'show']);
    Route::put('/{id}', [GeneratorController::class, 'update']);
    Route::delete('/{id}', [GeneratorController::class, 'destroy']);
});

Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::get('{id}', [ReportController::class, 'show']);
    Route::post('/', [ReportController::class, 'store']);
    Route::put('/', [ReportController::class, 'update']);
    Route::delete('{id}', [ReportController::class, 'destroy']);
    Route::post('{reportId}/tasks', [ReportController::class, 'addTask']);
    Route::delete('{reportId}/tasks/{taskId}', [ReportController::class, 'deleteTask']);
    Route::delete('{reportId}/notes/{noteId}', [ReportController::class, 'deleteNote']);
    Route::delete('{reportId}/parts/{partId}', [ReportController::class, 'deletePart']);
});
