<?php

namespace Hms5232\LaravelTwinCache\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Hms5232\LaravelTwinCache\TwinCacheServiceProvider',
        ];
    }

    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return ['hms522/laravel-twin-cache'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // change cache config
        $app['config']->set('cache.default', 'twin');
        $app['config']->set('cache.prefix', 'twin');
        $app['config']->set('cache.stores', [
            'twin' => [
                'driver' => 'twin',
                'older' => 'array',
                'younger' => 'file',
                'sync_ttl' => 120,
            ],
        ]);
    }

    /**
     * Get application timezone.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return 'Asia/Taipei';
    }
}
