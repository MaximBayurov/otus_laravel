<?php

namespace App\Providers;

use App\Enums\Permissions\Admin;
use App\Models\User;
use App\Policies\AdminExportPolicy;
use App\Policies\AdminImportPolicy;
use App\Repositories\UsersRepository;
use App\Services\CacheHelper;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Services\ITelegramBotService;
use App\Services\TelegramBotService;
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
        $this->app->singleton(ITelegramBotService::class, function (Application $app) {
            return new TelegramBotService(
                env('TELEGRAM_BOT_USERNAME', ''),
                env('TELEGRAM_BOT_API_KEY', ''),
                env('TELEGRAM_WEBHOOK_SECRET'),
                env('TELEGRAM_BOT_ADMIN_ID'),
                [
                    app_path() . "/Telegram/Commands"
                ]
            );
        });
        $this->app->singleton(UsersRepository::class, function (Application $app) {
            return new UsersRepository();
        });
        $this->app->singleton(ExportService::class, function (Application $app) {
            return new ExportService();
        });
        $this->app->singleton(ImportService::class, function (Application $app) {
            return new ImportService();
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
        Gate::define('admin.import', [AdminImportPolicy::class, 'index']);
        Gate::define('admin.import.model', [AdminImportPolicy::class, 'canImport']);
        Gate::define('admin.export', [AdminExportPolicy::class, 'index']);
        Gate::define('admin.export.model', [AdminExportPolicy::class, 'canExport']);
    }
}
