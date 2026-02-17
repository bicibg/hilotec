<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a site setting by "group.key" notation.
     *
     * Usage: setting('contact.phone_support_infra') → "+41 34 408 01 00"
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
