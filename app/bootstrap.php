<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Database;

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/Core/helpers.php';

$configFile = is_file(BASE_PATH . '/config/config.php')
    ? BASE_PATH . '/config/config.php'
    : BASE_PATH . '/config/config.example.php';

Config::load(require $configFile);

date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));

if (Config::get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

foreach (['products', 'logs', 'uploads'] as $dir) {
    $path = (string) Config::get('storage.' . $dir);
    if ($path !== '' && !is_dir($path)) {
        @mkdir($path, 0775, true);
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

Database::configure((array) Config::get('db'));
