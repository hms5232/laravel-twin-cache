<?php

namespace Hms5232\LaravelTwinCache\Tests;

use Illuminate\Support\Facades\Cache;

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

            // Laravel bulit-in
            'array' => [
                'driver' => 'array',
                'serialize' => false,
            ],
            'file' => [
                'driver' => 'file',
                'path' => storage_path('framework/cache/data'),
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

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->getOlderStore()->flush();
        $this->getYoungerStore()->flush();

        parent::tearDown();
    }

    /**
     * Get twin store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getTwinStore(): \Illuminate\Contracts\Cache\Repository
    {
        return Cache::store('twin');
    }

    /**
     * Get older store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getOlderStore(): \Illuminate\Contracts\Cache\Repository
    {
        // This name should same as above config.
        return Cache::store('array');
    }

    /**
     * Get younger store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getYoungerStore(): \Illuminate\Contracts\Cache\Repository
    {
        // This name should same as above config.
        return Cache::store('file');
    }
}
