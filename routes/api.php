<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandImportController;
use App\Http\Controllers\CapacityController;
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


Route::post('brands/import', [BrandImportController::class, 'import'])
    ->middleware('permission:site.create|site.update');
Route::post('brands', [BrandImportController::class, 'store'])
    ->middleware('permission:site.create');

// MTN Sites
Route::apiResource('mtn-sites', MtnSiteController::class)
    ->middleware([
        'index'   => 'permission:site.get',
        'show'    => 'permission:site.get|site.view',
        'store'   => 'permission:site.create',
        'update'  => 'permission:site.update',
        'destroy' => 'permission:site.delete',
    ]);
Route::delete('mtn-sites', [MtnSiteController::class, 'destroyBatch'])
    ->middleware('permission:site.delete');
Route::post('mtn-sites/search', [MtnSiteController::class, 'index'])
    ->middleware('permission:site.get');
Route::get('mtn-sites/generators/{id}', [MtnSiteController::class, 'getGenerator'])
    ->middleware('permission:site.get');
Route::delete('/mtn-sites/{site}/generators/unlink', [MtnSiteController::class, 'unlinkGenerators'])
    ->middleware('permission:site.update');
Route::post('mtn-sites/import', [MtnSiteController::class, 'importExcel'])
    ->middleware('permission:site.create');

// Capacities
Route::prefix('capacities')->group(function () {
    Route::get('/',   [CapacityController::class, 'index'])->middleware('permission:site.get');
    Route::post('/',  [CapacityController::class, 'store'])->middleware('permission:site.create');
    Route::put('/{id}', [CapacityController::class, 'update'])->middleware('permission:site.update');
    Route::delete('/', [CapacityController::class, 'destroyList'])->middleware('permission:site.delete');
});

// Brands
Route::prefix('brands')->group(function () {
    Route::get('/',    [BrandController::class, 'index'])->middleware('permission:site.get');
    Route::post('/',   [BrandController::class, 'store'])->middleware('permission:site.create');
    Route::put('/{brand}', [BrandController::class, 'update'])->middleware('permission:site.update');
    Route::delete('/', [BrandController::class, 'destroy'])->middleware('permission:site.delete');
});

// Engines
Route::apiResource('engines', EngineController::class)
    ->only(['index', 'store', 'update'])
    ->middleware([
        'index'  => 'permission:site.get',
        'store'  => 'permission:site.create',
        'update' => 'permission:site.update',
    ]);
Route::delete('engines', [EngineController::class, 'destroy'])
    ->middleware('permission:site.delete');
Route::get('/engines/parts/{id}', [EngineController::class, 'getPartsByEngine'])
    ->name('engines.parts')
    ->whereNumber('engine')
    ->middleware('permission:site.get');

// Parts
Route::prefix('parts')->group(function () {
    Route::get('/',   [PartController::class, 'index'])->middleware('permission:site.get');
    Route::post('/',  [PartController::class, 'store'])->middleware('permission:site.create');
    Route::put('/{id}', [PartController::class, 'update'])->middleware('permission:site.update');
    Route::delete('/', [PartController::class, 'destroy'])->middleware('permission:site.delete');
});

// Generators
Route::prefix('generators')->group(function () {
    Route::get('/unassigned', [GeneratorController::class, 'getUnassigned'])
        ->middleware('permission:site.get');
    Route::get('/', [GeneratorController::class, 'index'])
        ->middleware('permission:site.get');
    Route::post('/', [GeneratorController::class, 'store'])
        ->middleware('permission:site.create');
    Route::get('/{id}', [GeneratorController::class, 'show'])
        ->middleware('permission:site.get');
    Route::put('/{id}', [GeneratorController::class, 'update'])
        ->middleware('permission:site.update');
    Route::delete('/{id}', [GeneratorController::class, 'destroy'])
        ->middleware('permission:site.delete');
    Route::post('/import', [GeneratorController::class, 'import'])
        ->middleware('permission:site.create');
});
Route::post('/mtn-sites/{site}/assign-generators', [GeneratorController::class, 'assignGeneratorsToSite'])
    ->middleware('permission:site.update');

// Reports
Route::prefix('reports')->group(function () {
    Route::get('/',            [ReportController::class, 'index'])
        ->middleware('permission:report.view');
    Route::get('{id}',         [ReportController::class, 'show'])
        ->middleware('permission:report.get');
    Route::post('/',           [ReportController::class, 'store'])
        ->middleware('permission:report.create');
    Route::put('/',            [ReportController::class, 'update'])
        ->middleware('permission:report.update');
    Route::delete('/{id}',     [ReportController::class, 'destroy'])
        ->middleware('permission:report.delete');
    Route::post('{reportId}/tasks',       [ReportController::class, 'addTask'])
        ->middleware('permission:report.add-task');
    Route::delete('{reportId}/tasks',     [ReportController::class, 'deleteTasks'])
        ->middleware('permission:report.delete-tasks');
    Route::delete('{reportId}/notes',     [ReportController::class, 'deleteNotes'])
        ->middleware('permission:report.delete-notes');
    Route::delete('{reportId}/parts',     [ReportController::class, 'deleteParts'])
        ->middleware('permission:report.delete-parts');
    Route::post('/{reportId}/add-note',   [ReportController::class, 'addTechnicianNote'])
        ->middleware('permission:report.add-note');
    Route::post('/{reportId}/add-part',   [ReportController::class, 'addPart'])
        ->middleware('permission:report.add-part');
    Route::post('/export',                [ReportController::class, 'exportReports'])
        ->name('reports.export')
        ->middleware('permission:report.export');
});
