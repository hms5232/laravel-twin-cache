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

    /**
     * test of get().
     *
     * @return void
     */
    public function testGet()
    {
        $this->assertNull($this->getOlderStore()->get('single'));
        $this->assertSame('dog', $this->getOlderStore()->get('single', 'dog'));

        $this->getOlderStore()->put('cat', 'meow');
        $this->assertSame('meow', $this->getOlderStore()->get('cat'));
    }

    /**
     * test of put().
     *
     * @return void
     */
    public function testPut()
    {
        $this->assertNull($this->getOlderStore()->get('rice'));
        $this->getOlderStore()->put('rice', 'Donburi');
        $this->assertSame('Donburi', $this->getOlderStore()->get('rice'));
    }

    /**
     * test of forget().
     *
     * @return void
     */
    public function testForget()
    {
        $this->getOlderStore()->put('Donburi', 'jp');
        $this->getOlderStore()->put('risotto', 'it');

        $this->getOlderStore()->forget('risotto');
        $this->assertSame('jp', $this->getOlderStore()->get('Donburi'));
        $this->assertNull($this->getOlderStore()->get('risotto'));
    }

    /**
     * test of flush().
     *
     * @return void
     */
    public function testFlush()
    {
        $this->getOlderStore()->put('Donburi', 'jp');
        $this->getOlderStore()->put('risotto', 'it');

        $this->getOlderStore()->flush();
        $this->assertNull($this->getOlderStore()->get('Donburi'));
        $this->assertNull($this->getOlderStore()->get('risotto'));
    }

    /**
     * test of getTwin().
     *
     * @return void
     */
    public function testGetTwin()
    {
        // test default value
        $this->assertNull($this->getTwinStore()->getTwin('single'));
        $this->assertSame('dog', $this->getTwinStore()->getTwin('single', 'dog'));

        // not exist in younger store
        $this->getOlderStore()->put('cat', 'meow');
        $this->assertSame('meow', $this->getOlderStore()->get('cat'));
        $this->assertNull($this->getYoungerStore()->get('cat'));
        $this->assertSame('meow', $this->getTwinStore()->getTwin('cat'));

        // not exist in older store
        $this->getYoungerStore()->put('turtle', 'run');
        $this->assertNull($this->getOlderStore()->get('turtle'));
        $this->assertSame('run', $this->getYoungerStore()->get('turtle'));
        $this->assertSame('run', $this->getTwinStore()->getTwin('turtle'));
        $this->assertSame('run', $this->getOlderStore()->get('turtle'));  // sync younger to older

        // key exist both stores
        $this->getOlderStore()->put('candy', 'apple');
        $this->getYoungerStore()->put('candy', 'apple');
        $this->assertSame('apple', $this->getOlderStore()->get('candy'));
        $this->assertSame('apple', $this->getYoungerStore()->get('candy'));
        $this->assertSame('apple', $this->getTwinStore()->getTwin('candy'));

        // key not exist both stores
        $this->assertNull($this->getOlderStore()->get('yukata'));
        $this->assertNull($this->getYoungerStore()->get('yukata'));
        $this->assertNull($this->getTwinStore()->getTwin('yukata'));
    }

    /**
     * test of putTwin().
     *
     * @return void
     */
    public function testPutTwin()
    {
        $this->assertNull($this->getOlderStore()->get('forest'));
        $this->assertNull($this->getYoungerStore()->get('forest'));

        $this->getTwinStore()->putTwin('forest', 'random');
        $this->assertSame('random', $this->getOlderStore()->get('forest'));
        $this->assertSame('random', $this->getYoungerStore()->get('forest'));
    }

    /**
     * test of forgetTwin().
     *
     * @return void
     */
    public function testForgetTwin()
    {
        $this->getOlderStore()->put('Donburi', 'jp');
        $this->getOlderStore()->put('risotto', 'it');
        $this->getYoungerStore()->put('Donburi', 'jp');
        $this->getYoungerStore()->put('risotto', 'it');

        $this->getTwinStore()->forgetTwin('risotto');
        $this->assertSame('jp', $this->getOlderStore()->get('Donburi'));
        $this->assertNull($this->getOlderStore()->get('risotto'));
        $this->assertSame('jp', $this->getYoungerStore()->get('Donburi'));
        $this->assertNull($this->getYoungerStore()->get('risotto'));
    }

    /**
     * test of flushTwin().
     *
     * @return void
     */
    public function testFlushTwin()
    {
        $this->getOlderStore()->put('Donburi', 'jp');
        $this->getOlderStore()->put('risotto', 'it');
        $this->getYoungerStore()->put('Donburi', 'jp');
        $this->getYoungerStore()->put('risotto', 'it');

        $this->getTwinStore()->flushTwin();
        $this->assertNull($this->getOlderStore()->get('Donburi'));
        $this->assertNull($this->getOlderStore()->get('risotto'));
        $this->assertNull($this->getYoungerStore()->get('Donburi'));
        $this->assertNull($this->getYoungerStore()->get('risotto'));
    }
}
