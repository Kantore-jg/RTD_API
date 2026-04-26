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
        $versionKey = "org:{$orgId}:{$resource}:version";
        $current = (int) Cache::get($versionKey, 0);
        Cache::put($versionKey, $current + 1, 86400);
        Cache::forget("org:{$orgId}:{$resource}");
    }

    protected function versionedOrgCacheKey(string $resource, int $orgId, string $suffix = ''): string
    {
        $versionKey = "org:{$orgId}:{$resource}:version";
        $version = (int) Cache::get($versionKey, 0);
        $base = "org:{$orgId}:{$resource}:v{$version}";

        return $suffix ? "{$base}:{$suffix}" : $base;
    }
}
