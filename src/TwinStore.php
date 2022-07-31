<?php


namespace Hms5232\LaravelTwinCache;


use Illuminate\Contracts\Cache\Store;

class TwinStore implements Store
{
    /**
     * A string that should be prepended to key.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new Twin store.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPrefix();
    }

	public function get($key) {}

	public function many(array $keys) {}

	public function put($key, $value, $seconds) {}

	public function putMany(array $values, $seconds) {}

	public function increment($key, $value = 1) {}

	public function decrement($key, $value = 1) {}

	public function forever($key, $value) {}

	public function forget($key) {}

	public function flush() {}

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
	public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @return void
     */
    public function setPrefix()
    {
        $this->prefix = config('cache.prefix') . '_' ?: '';
    }
}
