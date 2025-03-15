<?php

use App\Http\Controllers\AuthController;
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

    Route::group(['middleware' => 'role:site_manager'], function () {
        Route::post('site/store', [SiteInfrastructureController::class, 'storeAllData']);
        Route::delete('sites/delete', [SiteInfrastructureController::class, 'deleteSites']);
        Route::put('sites/{id}', [SiteInfrastructureController::class, 'update']);
    });

    Route::group(['middleware' => 'role:site_manager|sites_viewer|own_sites_viewer'], function () {
        Route::get('sites', [SiteInfrastructureController::class, 'index']);
        Route::get('sites/{id}', [SiteInfrastructureController::class, 'showSite']);
        Route::post('sites/export', [SiteInfrastructureController::class, 'exportSelectedSites'])
            ->name('sites.export.selected');
        Route::get('site-images/{siteId}/{type}', [SiteInfrastructureController::class, 'getSiteImages']);
        Route::get('images/{siteId}/{type}', [SiteInfrastructureController::class, 'getImages']);
    });

    Route::group(['middleware' => 'role:user_manager'], function () {
        Route::post('admin/generate-user', [AuthController::class, 'register']);
        Route::get('users', [UserController::class, 'getUsers']);
        Route::delete('users-delete', [UserController::class, 'deleteUsers']);
        Route::put('users-edit', [UserController::class, 'updateUser']);
    });

    Route::get('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);
