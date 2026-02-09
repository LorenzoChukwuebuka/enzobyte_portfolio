<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

function setting($key, $default = null)
{
    return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
        return DB::table('site_settings')
            ->where('key', $key)
            ->value('value') ?? $default;
    });
}