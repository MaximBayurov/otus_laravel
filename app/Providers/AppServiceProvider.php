<?php

namespace App\Providers;

use App\Enums\Permissions\Admin;
use App\Models\User;
use Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
