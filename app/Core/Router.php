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

        $route = $this->routes[$method][$path] ?? null;

        if (!$route) {
            return Response::json([
                'error' => true,
                'message' => 'Route not found.',
            ], 404);
        }

        $handler = $route['handler'];
        $callable = $this->resolveHandler($handler);
        return $callable($request);
    }

    private function resolveHandler($handler): callable
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = $this->resolveController($class);
            return function (Request $request) use ($instance, $method) {
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
