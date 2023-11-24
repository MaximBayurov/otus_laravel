<?php

namespace App\Providers;

use App\Services\ConstructionService;
use App\Services\LanguageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppHelpersProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LanguageService::class, function (Application $app) {
            return new LanguageService();
        });
        $this->app->singleton(ConstructionService::class, function (Application $app) {
            return new ConstructionService();
        });
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            LanguageService::class,
            ConstructionService::class,
        ];
    }
}
