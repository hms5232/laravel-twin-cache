# laravel-twin-cache
Use second cache flexibly in Laravel.

## Installation

```
composer require hms5232/laravel-twin-cache
```

## Usage

### config

Add `twin` store in `config/cache.php`:

```php
'stores' => [
    // other store config here

    'twin' => [
        'driver' => 'twin',
        'older' => 'redis',  // First/preferred cache
        'younger' => 'database',  // Second/backup cache
        'sync_ttl' => 120,  // TTL for younger synced to older. Default is null => forever
    ],
],
```

Change cache drive in `.env`:

```
CACHE_DRIVER=twin
```

### method

All Laravel built-in methods modify the `older` cache, and you can add suffix `Twin` to method name for twin cache.

For example, you want to update both older and younger cache:

```php
Cache::put('foo', 'bar');
// change "put" to "putTwin"
Cache::putTwin('foo', 'bar');
```

Another example, a key is in younger cache drive but doesn't exist in older. You want to sync this key when select this key:

```php
// only select older cache
Cache::get('foo');

// This will select older cache first
// If no result, select younger, else return result
// 1. If exists in younger, insert into older cache and return
// 2. If it doesn't exist, return default value
Cache::getTwin('foo', 'bar');
```

So you can use second cache flexibly depend on need.

#### Method List

All parameters are same.

| Laravel built-in | twin cache       |
|------------------|------------------|
| `get`            | `getTwin`        |
| `many`           |                  |
| `put`            | `putTwin`        |
| `putMany`        | `putManyTwin`    |
| `increment`      | `incrementTwin`  |
| `descrement`     | `descrementTwin` |
| `forever`        | `foreverTwin`    |
| `forget`         | `forgetTwin`     |
| `flush`          | `flushTwin`      |
| `has`            | `hasTwin`        |

## LICENSE

Copyright (c) 2022 hms5232

See [LICENSE](LICENSE).
