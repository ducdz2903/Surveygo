<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Basic router with support for HTTP verbs and middleware.
 */
class Router
{
    /**
     * @var array<string, array<string, array{handler:mixed}>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->uri();

        // First try exact match
        $route = $this->routes[$method][$path] ?? null;

        if (!$route) {
            // Try pattern matching for dynamic routes
            $route = $this->matchDynamicRoute($method, $path);
        }

        if (!$route) {
            return Response::json([
                'error' => true,
                'message' => 'Route not found.',
            ], 404);
        }

        $handler = $route['handler'];
        $callable = $this->resolveHandler($handler, $route['params'] ?? []);
        return $callable($request);
    }

    private function matchDynamicRoute(string $method, string $path): ?array
    {
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => $route) {
            // Convert pattern like /surveys/{id}/questions to regex
            $regexPattern = preg_replace(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                '(?P<$1>[0-9]+)',
                $pattern
            );
            $regexPattern = '#^' . $regexPattern . '$#';

            if (preg_match($regexPattern, $path, $matches)) {
                // Filter out numeric keys from matches (keep only named groups)
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $params[$key] = $value;
                    }
                }
                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    private function resolveHandler($handler, array $params = []): callable
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = $this->resolveController($class);
            return function (Request $request) use ($instance, $method, $params) {
                // Inject params into request attributes
                foreach ($params as $key => $value) {
                    $request->setAttribute($key, $value);
                }
                return $instance->$method($request);
            };
        }

        if (is_callable($handler)) {
            return $handler;
        }

        throw new \InvalidArgumentException('Route handler is not callable.');
    }

    private function resolveController(string $class): Controller
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Controller {$class} not found.");
        }

        $controller = new $class();
        if (!$controller instanceof Controller) {
            throw new \RuntimeException("Controller {$class} must extend " . Controller::class);
        }

        return $controller;
    }
}
