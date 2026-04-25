<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CachesQueries
{
    protected function cacheKey(string $prefix, ...$parts): string
    {
        return $prefix . ':' . implode(':', $parts);
    }

    protected function cached(string $key, int $ttl, \Closure $callback): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    protected function forgetCachePrefix(string $prefix): void
    {
        Cache::forget($prefix);
    }

    protected function orgCacheKey(string $resource, int $orgId): string
    {
        return "org:{$orgId}:{$resource}";
    }

    protected function clearOrgCache(string $resource, int $orgId): void
    {
        Cache::forget($this->orgCacheKey($resource, $orgId));
    }
}
