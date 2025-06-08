<?php

namespace App\Providers;

use App\Repositories\BrandRepository;
use App\Repositories\CapacityRepository;
use App\Repositories\CompletedTaskRepository;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CapacityRepositoryInterface;
use App\Repositories\Contracts\CompletedTaskRepositoryInterface;
use App\Repositories\Contracts\EngineRepositoryInterface;
use App\Repositories\Contracts\PartRepositoryInterface;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\Contracts\SiteRepositoryInterface;
use App\Repositories\EngineRepository;
use App\Repositories\GeneratorRepository;
use App\Repositories\Contracts\GeneratorRepositoryInterface;
use App\Repositories\PartRepository;
use App\Repositories\ReportRepository;
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
        $this->app->bind(GeneratorRepositoryInterface::class, GeneratorRepository::class);
        $this->app->bind(CompletedTaskRepositoryInterface::class, CompletedTaskRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
