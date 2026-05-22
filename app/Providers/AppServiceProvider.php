<?php

namespace App\Providers;

use App\Services\SettingsService;
use App\Services\AchievementService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, fn () => new SettingsService());
        $this->app->singleton(AchievementService::class, fn () => new AchievementService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            /** @var SettingsService $settings */
            $settings = app(SettingsService::class);

            $view->with('appSettings', $settings->all());
        });
    }
}
