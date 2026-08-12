<?php

declare(strict_types=1);

namespace Fandoogh\Core;

defined('ABSPATH') || exit;

final class Config
{
    /**
     * Load a configuration file.
     *
     * @param string $file Configuration filename without extension.
     * @return array<string, mixed>
     */
    public static function load(string $file): array
    {
        $path = FA_PATH . 'config/' . $file . '.php';

        if (! is_file($path)) {
            return [];
        }

        return require $path;
    }
}