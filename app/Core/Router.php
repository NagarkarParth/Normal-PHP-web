<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array{0: class-string, 1: string}}> */
    private array $routes = [];

    /** @param array{0: class-string, 1: string} $handler */
    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param array{0: class-string, 1: string} $handler */
    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /** @param array{0: class-string, 1: string} $handler */
    private function add(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern === '/' ? '/' : rtrim($pattern, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        $path = $request->path();
        $method = $request->method();
        $pathMatched = false;

        foreach ($this->routes as $route) {
            $params = $this->match($route['pattern'], $path);
            if ($params === null) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }

            [$class, $action] = $route['handler'];
            /** @var Controller $controller */
            $controller = new $class($request);
            $controller->{$action}(...array_values($params));

            return;
        }

        $this->abort($pathMatched ? 405 : 404);
    }

    /** @return array<string, string>|null */
    private function match(string $pattern, string $path): ?array
    {
        if (!str_contains($pattern, '{')) {
            return $pattern === $path ? [] : null;
        }

        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        if (!is_string($regex)) {
            return null;
        }

        if (preg_match('#^' . $regex . '$#', $path, $matches) !== 1) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function abort(int $status): void
    {
        http_response_code($status);
        $view = new View();
        echo $view->render('errors/' . $status, ['status' => $status], 'app');
    }
}
