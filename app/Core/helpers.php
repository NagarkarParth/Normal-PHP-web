<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\Settings;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    $base = rtrim((string) Config::get('app.url', ''), '/');

    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function uploaded(?string $path, string $fallback = 'assets/img/placeholder.svg'): string
{
    if ($path === null || $path === '') {
        return url($fallback);
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    return url(ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function auth(): ?array
{
    return Auth::user();
}

function is_admin(): bool
{
    return Auth::isAdmin();
}

function setting(string $key, mixed $default = null): mixed
{
    return Settings::get($key, $default);
}

function currency_symbol(): string
{
    $map = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'AUD' => 'A$', 'CAD' => 'C$'];
    $code = (string) Settings::get('currency', 'INR');

    return $map[$code] ?? ($code . ' ');
}

function money(float|int|string $amount): string
{
    return currency_symbol() . number_format((float) $amount, 2);
}

function str_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';

    return trim($value, '-');
}

function excerpt(string $value, int $length = 120): string
{
    $value = trim(strip_tags($value));
    if (mb_strlen($value) <= $length) {
        return $value;
    }

    return mb_substr($value, 0, $length) . '…';
}

function active_class(string $prefix, string $class = 'active'): string
{
    $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
    if ($prefix === '/') {
        return rtrim($path, '/') === '' ? $class : '';
    }

    return str_starts_with($path, $prefix) ? $class : '';
}

function order_status_color(string $status): string
{
    return match ($status) {
        'paid' => 'success',
        'pending' => 'warning',
        'failed' => 'danger',
        'refunded' => 'secondary',
        default => 'info',
    };
}
