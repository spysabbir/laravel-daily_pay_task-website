<?php

use App\Models\DefaultSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

// Get Default Settings
if (!function_exists('get_default_settings')) {
    function get_default_settings($key, $default = null)
    {
        $default_settings = Cache::remember('default_settings', 60 * 60, function () {
            return DefaultSetting::first();
        });

        return $default_settings->$key ?? $default;
    }
}

// Get Site Settings
if (!function_exists('get_site_settings')) {
    function get_site_settings($key, $default = null)
    {
        $site_settings = Cache::remember('site_settings', 60 * 60, function () {
            return SiteSetting::first();
        });

        return $site_settings->$key ?? $default;
    }
}
