<?php

declare(strict_types=1);

/**
 * Entry point for PHP's built-in server:
 *   php -S localhost:8000 -t public router.php
 */
$path = parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$file = __DIR__ . '/public' . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/public/index.php';
