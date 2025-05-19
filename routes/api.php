<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandImportController;
use App\Http\Controllers\SiteInfrastructureController;
use App\Http\Controllers\UserController;
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
