<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    /**
     * Get a setting value by "group.key" notation.
     * Results are cached for 60 minutes.
     */
    public static function get(string $dotKey, mixed $default = null): mixed
    {
        $settings = Cache::remember('settings.all', 3600, function () {
            return static::all()
                ->mapWithKeys(fn ($s) => ["{$s->group}.{$s->key}" => $s->value]);
        });

        return $settings->get($dotKey, $default);
    }

    /**
     * Set a setting value by "group.key" notation and clear cache.
     */
    public static function set(string $dotKey, ?string $value): void
    {
        [$group, $key] = explode('.', $dotKey, 2);

        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );

        Cache::forget('settings.all');
    }
}
