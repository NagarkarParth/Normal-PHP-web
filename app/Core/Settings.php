<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

final class Settings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public static function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }
}
