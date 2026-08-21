<?php

declare(strict_types=1);

use App\Core\Request;
use App\Core\View;

require dirname(__DIR__) . '/app/bootstrap.php';

/** @var App\Core\Router $router */
$router = require BASE_PATH . '/app/routes.php';
$request = new Request();

try {
    $router->dispatch($request);
} catch (Throwable $exception) {
    http_response_code(500);
    error_log((string) $exception);

    echo (new View())->render('errors/500', [
        'pageTitle' => 'Something went wrong',
        'exception' => App\Core\Config::get('app.debug') ? $exception : null,
    ], 'app');
}
