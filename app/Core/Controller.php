<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render view template dengan layout.
     */
    protected function view(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $viewPath) . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException("View file tidak ditemukan: {$viewFile}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === 'none' || empty($layout)) {
            echo $content;
            return;
        }

        $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new \RuntimeException("Layout file tidak ditemukan: {$layoutFile}");
        }

        require $layoutFile;
    }

    protected function json(array $data, int $status = 200): never
    {
        json($data, $status);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    protected function back(string $fallback = '/'): never
    {
        back($fallback);
    }
}
