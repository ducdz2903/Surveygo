<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal HTTP response helper for JSON APIs.
 */
class Response
{
    private int $status;
    private array $headers;
    private string $body;

    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function json(array $data, int $status = 200, array $headers = []): self
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new self($payload ?: '{}', $status, array_merge(['Content-Type' => 'application/json'], $headers));
    }

    public static function html(string $html, int $status = 200, array $headers = []): self
    {
        return new self($html, $status, array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers));
    }

    public static function redirect(string $location, int $status = 302, array $headers = []): self
    {
        return new self('', $status, array_merge(['Location' => $location], $headers));
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        echo $this->body;
    }
}
