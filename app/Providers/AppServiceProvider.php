<?php

namespace App\Providers;

use App\Repositories\BrandRepository;
use App\Repositories\CapacityRepository;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CapacityRepositoryInterface;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Repositories\Contracts\PartRepositoryInterface;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Repositories\EngineRepository;
use App\Repositories\PartRepository;
use App\Repositories\SiteRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SiteRepositoryInterface::class, SiteRepository::class);
        $this->app->bind(CapacityRepositoryInterface::class, CapacityRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(EngineRepositoryInterface::class, EngineRepository::class);
        $this->app->bind(PartRepositoryInterface::class, PartRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
