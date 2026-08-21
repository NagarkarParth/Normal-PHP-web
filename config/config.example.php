<?php
/**
 * Copy this file to config/config.php and adjust for your environment.
 * Environment variables (if present) always win, which keeps deployments 12-factor friendly.
 */

$env = static function (string $key, $default = null) {
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    if ($value === 'true') { return true; }
    if ($value === 'false') { return false; }
    return $value;
};

return [
    'app' => [
        'name'     => $env('APP_NAME', 'YBT Digital'),
        'url'      => rtrim((string) $env('APP_URL', 'http://localhost:8000'), '/'),
        'debug'    => (bool) $env('APP_DEBUG', true),
        'timezone' => $env('APP_TIMEZONE', 'Asia/Kolkata'),
        'key'      => $env('APP_KEY', 'change-this-to-a-long-random-string'),
    ],
    'db' => [
        'host'     => $env('DB_HOST', '127.0.0.1'),
        'port'     => (int) $env('DB_PORT', 3306),
        'database' => $env('DB_DATABASE', 'ybt_digital'),
        'username' => $env('DB_USERNAME', 'root'),
        'password' => $env('DB_PASSWORD', ''),
        'charset'  => 'utf8mb4',
    ],
    'storage' => [
        // Digital product files live outside the web root and are streamed by the app.
        'products' => $env('STORAGE_PRODUCTS', dirname(__DIR__) . '/storage/products'),
        'logs'     => $env('STORAGE_LOGS', dirname(__DIR__) . '/storage/logs'),
        'uploads'  => $env('STORAGE_UPLOADS', dirname(__DIR__) . '/public/uploads'),
    ],
    'mail' => [
        // "log" writes messages to storage/logs/mail.log, "mail" uses PHP's mail().
        'driver' => $env('MAIL_DRIVER', 'log'),
        'from'   => $env('MAIL_FROM', 'no-reply@ybtdigital.test'),
    ],
];
