<?php

use App\Models\DefaultSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('get_default_settings')) {
    function get_default_settings($key, $default = null)
    {
        $default_settings = Cache::remember('default_settings', 60 * 60, function () {
            return DefaultSetting::first();
        });

        return $default_settings->$key ?? $default;
    }
}

if (!function_exists('get_all_default_settings')) {
    function get_all_default_settings()
    {
        return Cache::remember('default_settings', 60 * 60, function () {
            return DefaultSetting::first();
        });
    }
}
