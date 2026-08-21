<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Setting
{
    /** @var array<string, string>|null */
    private static ?array $cache = null;

    /** @return array<string, string> */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $rows = Database::all('SELECT setting_key, setting_value FROM settings');
        $items = [];
        foreach ($rows as $row) {
            $items[(string) $row['setting_key']] = (string) $row['setting_value'];
        }

        return self::$cache = $items;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        return array_key_exists($key, $all) && $all[$key] !== '' ? $all[$key] : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        Database::run(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            [$key, (string) $value]
        );
        self::$cache = null;
    }

    /** @param array<string, mixed> $values */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::set($key, $value);
        }
    }

    /** @return array{site_name: string, logo: string, tagline: string, footer_text: string, support_email: string} */
    public static function branding(): array
    {
        return [
            'site_name'     => (string) self::get('site_name', 'YBT Digital'),
            'logo'          => (string) self::get('site_logo', ''),
            'tagline'       => (string) self::get('site_tagline', 'Premium digital products, delivered instantly.'),
            'footer_text'   => (string) self::get('footer_text', '© ' . date('Y') . ' YBT Digital. All rights reserved.'),
            'support_email' => (string) self::get('support_email', 'support@ybtdigital.test'),
        ];
    }

    public static function activeGateway(): string
    {
        return (string) self::get('payment_gateway', 'offline');
    }

    public static function notificationEnabled(string $key): bool
    {
        return (string) self::get('notify_' . $key, '1') === '1';
    }
}
