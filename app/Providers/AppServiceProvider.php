<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\InfobipService;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InfobipService::class, function ($app) {
            return new InfobipService();
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
