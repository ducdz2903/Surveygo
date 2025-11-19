<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple HTTP request wrapper with helpers for JSON APIs.
 */
class Request
{
    private string $method;
    private string $uri;
    private array $query;
    private array $body;
    private array $headers;
    private array $server;
    private array $attributes = [];

    private bool $jsonParsed = false;
    private array $jsonPayload = [];

    public function __construct(string $method, string $uri, array $query, array $body, array $headers, array $server)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->query = $query;
        $this->body = $body;
        $this->headers = $headers;
        $this->server = $server;
    }

    public static function capture(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/';

        // Compute app base from script name, e.g. "/Surveyon/public/index.php"
        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = str_replace('\\', '/', $scriptName);
        $basePath = rtrim(dirname($basePath), '/');

        $appBase = $basePath;
        if ($appBase !== '' && (function_exists('str_ends_with') ? str_ends_with($appBase, '/public') : substr($appBase, -7) === '/public')) {
            $appBase = substr($appBase, 0, -strlen('/public'));
        }
        $appBase = rtrim($appBase, '/');

        // Normalize to a path relative to the app base so routes like "/" work under "/Surveyon"
        $path = $requestUri;
        if ($appBase !== '' && $appBase !== '/') {
            $len = strlen($appBase);
            if (stripos($requestUri, $appBase) === 0) {
                $path = substr($requestUri, $len);
            }
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        // Additional normalizations:
        // - collapse multiple slashes
        // - remove trailing slash (except for root)
        // - strip a trailing "/index.php" to map to root
        $path = preg_replace('#/{2,}#', '/', $path) ?? $path;
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        if ($path === '/index.php') {
            $path = '/';
        }

        return new self(
            $method,
            $path,
            $_GET,
            $_POST,
            function_exists('getallheaders') ? getallheaders() : [],
            $_SERVER
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function query(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function input(string $key = null, $default = null)
    {
        $payload = $this->isJson() ? $this->json() : $this->body;

        if ($key === null) {
            return $payload;
        }

        return $payload[$key] ?? $default;
    }

    public function header(string $key, $default = null)
    {
        foreach ($this->headers as $header => $value) {
            if (strcasecmp($header, $key) === 0) {
                return $value;
            }
        }

        return $default;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');
        if (!$header) {
            return null;
        }

        if (preg_match('/Bearer\\s+(.*)$/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function isJson(): bool
    {
        $contentType = $this->header('Content-Type') ?? $this->header('content-type');
        return $contentType && stripos($contentType, 'application/json') !== false;
    }

    public function json(): array
    {
        if ($this->jsonParsed) {
            return $this->jsonPayload;
        }

        $input = file_get_contents('php://input');
        $decoded = json_decode($input, true);
        $this->jsonParsed = true;
        $this->jsonPayload = is_array($decoded) ? $decoded : [];

        return $this->jsonPayload;
    }

    public function isDebug(): bool
    {
        $config = Container::get('config');
        return (bool)($config['app']['debug'] ?? false);
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
}
