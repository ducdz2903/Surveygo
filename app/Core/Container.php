<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Very small service container to share singletons across the app.
 */
class Container
{
    /**
     * @var array<string, mixed>
     */
    private static array $items = [];

    /**
     * Store a value or factory.
     *
     * @param string          $key
     * @param mixed|callable  $value
     */
    public static function set(string $key, $value): void
    {
        self::$items[$key] = $value;
    }

    /**
     * Retrieve a value, executing closures lazily for singleton registration.
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        if (!array_key_exists($key, self::$items)) {
            throw new \InvalidArgumentException("Container entry '{$key}' not found.");
        }

        $entry = self::$items[$key];
        if ($entry instanceof \Closure) {
            // Replace closure with its result to mimic singleton behavior.
            $entry = $entry();
            self::$items[$key] = $entry;
        }

        return $entry;
    }
}

