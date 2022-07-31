<?php

namespace Hms5232\LaravelTwinCache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class TwinCacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            Cache::extend('twin', function ($app) {
                return Cache::repository(new TwinStore());
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
