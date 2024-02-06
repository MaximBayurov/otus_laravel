<?php

namespace App\Providers;

use App\Services\CacheHelper;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ConstructionLanguageProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ConstructionsRepository::class, function (Application $app) {
            return new \App\Repositories\ConstructionsRepository(
                $app->get(CacheHelper::class)
            );
        });
        $this->app->singleton(LanguagesRepository::class, function (Application $app) {
            return new \App\Repositories\LanguagesRepository(
                $app->get(CacheHelper::class)
            );
        });
        $this->app->singleton(ConstructionLanguageRepository::class, function (Application $app) {
            return new \App\Repositories\ConstructionLanguageRepository(
                $app->get(CacheHelper::class)
            );
        });
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            ConstructionImplementationsService::class,
        ];
    }
}
