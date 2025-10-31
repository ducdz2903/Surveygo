<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple PHP view renderer.
 */
class View
{
    public function render(string $template, array $data = []): string
    {
        $path = BASE_PATH . '/app/Views/' . $template . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("View {$template} not found.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;

        return (string)ob_get_clean();
    }
}

