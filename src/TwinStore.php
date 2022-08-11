<?php

namespace Hms5232\LaravelTwinCache;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Cache;

class TwinStore implements Store
{
    /**
     * A string that should be prepended to key.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The first cache.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $older;

    /**
     * The second cache.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $younger;

    /**
     * The synced cache ttl.
     *
     * @var int
     */
    protected $ttl;

    /**
     * Create a new Twin store.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPrefix();
        $this->setTwinTtl();
        $this->older = $this->older();
        $this->younger = $this->younger();
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->older->get($key, $default);
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        return $this->older->many($keys);
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds = null)
    {
        return $this->older->put($key, $value, $seconds);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds = null)
    {
        return $this->older->putMany($values, $seconds);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->older->increment($key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->older->decrement($key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->older->forever($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->older->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->older->flush();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @return void
     */
    protected function setPrefix()
    {
        $this->prefix = config('cache.prefix') . '_' ?: '';
    }

    /**
     * Get the cache drive name.
     *
     * @param string $name
     * @return string
     */
    public function getDriveName($name)
    {
        return config('cache.stores.twin.' . $name);
    }

    /**
     * Get the older store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function older()
    {
        return Cache::store($this->getDriveName('older'));
    }

    /**
     * Get the older store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function younger()
    {
        return Cache::store($this->getDriveName('younger'));
    }

    /**
     * Get the ttl of synced cache.
     *
     * @return int
     */
    public function getTwinTtl()
    {
        return $this->ttl;
    }

    /**
     * Get the cache drive name.
     *
     * @return void
     */
    protected function setTwinTtl()
    {
        $this->ttl = config('cache.stores.twin.sync_ttl');
    }

    /**
     * Store an item already exist in second cache in the first cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    protected function syncTwin($key, $value, $seconds = null)
    {
        return $this->older->put($key, $value, $seconds ?? $this->getTwinTtl());
    }

    /**
     * Retrieve an item from the twin cache by key.
     *
     * @param  string|array  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getTwin($key, $default = null)
    {
        if ($this->older->has($key)) {
            return $this->older->get($key);
        } elseif ($this->younger->has($key)) {
            $this->syncTwin($key, $this->younger->get($key));
        }
        return $this->get($key, $default);
    }

    /**
     * Store an item in the twin cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function putTwin($key, $value, $seconds = null)
    {
        return $this->older->put($key, $value, $seconds)
            && $this->younger->put($key, $value, $seconds);
    }

    /**
     * Store multiple items in the twin cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putManyTwin(array $values, $seconds = null)
    {
        return $this->older->putMany($values, $seconds)
            && $this->younger->putMany($values, $seconds);
    }

    /**
     * Increment the value of an item in the twin cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function incrementTwin($key, $value = 1)
    {
        return $this->older->increment($key, $value)
            && $this->younger->increment($key, $value);
    }

    /**
     * Decrement the value of an item in the twin cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrementTwin($key, $value = 1)
    {
        return $this->older->decrement($key, $value)
            && $this->younger->decrement($key, $value);
    }

    /**
     * Store an item in the twin cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function foreverTwin($key, $value)
    {
        return $this->older->forever($key, $value)
            && $this->younger->forever($key, $value);
    }

    /**
     * Remove an item from the twin cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forgetTwin($key)
    {
        $forget_older = $this->older->forget($key);
        $forget_younger = $this->younger->forget($key);

        // If one of them succeed, this will return ture
        return $forget_older || $forget_younger;
    }

    /**
     * Remove all items from the twin cache.
     *
     * @return bool
     */
    public function flushTwin()
    {
        return $this->older->flush()
            && $this->younger->flush();
    }
}
