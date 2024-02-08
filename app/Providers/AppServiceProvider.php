<?php

namespace App\Providers;

use App\Enums\Permissions\Admin;
use App\Models\User;
use App\Services\CacheHelper;
use Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheHelper::class, function (Application $app) {
            return new CacheHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin.home', function (User $user) {
            return $user->havePermissionTo((Admin::VIEW)->code());
        });
    }
}
