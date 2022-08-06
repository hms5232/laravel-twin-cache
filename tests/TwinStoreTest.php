<?php

namespace Hms5232\LaravelTwinCache\Tests;

use Illuminate\Support\Facades\Cache;

class TwinStoreTest extends TestCase
{
    /**
     * test of getPrefix().
     *
     * @return void
     */
    public function testGetPrefix()
    {
        $this->assertSame('twin_', Cache::getPrefix());
    }

    /**
     * test of getTwinTtl().
     *
     * @return void
     */
    public function testGetTwinTtl()
    {
        $this->assertSame(120, Cache::getTwinTtl());
    }

    /**
     * test of getDriveName().
     *
     * @return void
     */
    public function testGetDriveName()
    {
        $this->assertSame('array', Cache::getDriveName('older'));
        $this->assertSame('file', Cache::getDriveName('younger'));
    }
}
