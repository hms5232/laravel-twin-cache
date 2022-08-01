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
     * @var string
     */
    protected $older;

    /**
     * The second cache.
     *
     * @var string
     */
    protected $younger;

    /**
     * The twin cache.
     *
     * @var array
     */
    protected $store;

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
        $this->setDriveName();
        $this->setPrefix();
        $this->setTwinTtl();
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        return Cache::store($this->older)->get($key);
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
        return Cache::store($this->older)->many($keys);
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        return Cache::store($this->older)->put($key, $value, $seconds);
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        return Cache::store($this->older)->putMany($values, $seconds);
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
        return Cache::store($this->older)->increment($key, $value);
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
        return Cache::store($this->older)->decrement($key, $value);
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
        return Cache::store($this->older)->forever($key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return Cache::store($this->older)->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        return Cache::store($this->older)->flush();
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
     * Set the store name of cache.
     *
     * @return void
     */
    protected function setDriveName()
    {
        $this->older = $this->getDriveName('older');
        $this->younger = $this->getDriveName('younger');
        $this->store = [
            $this->older,
            $this->younger
        ];
    }

    /**
     * Get the store.
     *
     * @return array
     */
    public function getTwinStores()
    {
        return $this->store;
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
        return Cache::store($this->older)->put($key, $value, $seconds ?? $this->getTwinTtl());
    }

    /**
     * Retrieve an item from the twin cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function getTwin($key)
    {
        if (Cache::store($this->older)->has($key)) {
            return Cache::store($this->older)->get($key);
        } elseif (Cache::store($this->younger)->has($key)) {
            $this->syncTwin($key, Cache::store($this->younger)->get($key));
        }
        return $this->get($key);
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
        return Cache::store($this->older)->put($key, $value, $seconds)
            && Cache::store($this->younger)->put($key, $value, $seconds);
    }

    /**
     * Store multiple items in the twin cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putManyTwin(array $values, $seconds)
    {
        return Cache::store($this->older)->putMany($values, $seconds)
            && Cache::store($this->younger)->putMany($values, $seconds);
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
        return Cache::store($this->older)->increment($key, $value)
            && Cache::store($this->younger)->increment($key, $value);
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
        return Cache::store($this->older)->decrement($key, $value)
            && Cache::store($this->younger)->decrement($key, $value);
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
        return Cache::store($this->older)->forever($key, $value)
            && Cache::store($this->younger)->forever($key, $value);
    }

    /**
     * Remove an item from the twin cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forgetTwin($key)
    {
        return Cache::store($this->older)->forget($key)
            && Cache::store($this->younger)->forget($key);
    }

    /**
     * Remove all items from the twin cache.
     *
     * @return bool
     */
    public function flushTwin()
    {
        return Cache::store($this->older)->flush()
            && Cache::store($this->younger)->flush();
    }
}
