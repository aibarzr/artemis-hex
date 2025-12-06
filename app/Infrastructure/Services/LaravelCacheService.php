<?php

namespace App\Infrastructure\Services;

use App\Domain\Shared\Ports\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

final class LaravelCacheService implements CacheServiceInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        return Cache::put($key, $value, now()->addSeconds($ttl));
    }

    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return Cache::remember($key, now()->addSeconds($ttl), $callback);
    }

    public function flush(): bool
    {
        return Cache::flush();
    }
}
