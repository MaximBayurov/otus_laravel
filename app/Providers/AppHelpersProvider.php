<?php

namespace App\Providers;

use App\Services\CacheHelper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppHelpersProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheHelper::class, function (Application $app) {
            return new CacheHelper();
        });
    }
}
