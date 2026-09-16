<?php

declare(strict_types=1);

namespace Fandoogh\Core;

defined('ABSPATH') || exit;

final class Autoloader
{
    /**
     * Module namespace prefix.
     */
    private const MODULE_NAMESPACE = 'Modules\\';

    /**
     * Module namespaces whose on-disk directory does not use the default
     * lowercase convention.
     *
     * @var array<string, string>
     */
    private const MODULE_DIRECTORIES = [
        'Reviews' => 'Reviews',
        'MegaMenu' => 'mega-menu',
    ];

    /**
     * Register autoloader.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * PSR-4 Autoloader.
     *
     * @param string $class Fully qualified class name.
     */
    private static function autoload(string $class): void
    {
        $prefix = 'Fandoogh\\';

        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));

        /*
        |--------------------------------------------------------------------------
        | APP Namespace
        |--------------------------------------------------------------------------
        */

        $appFile = FA_APP .
            str_replace('\\', DIRECTORY_SEPARATOR, $relative) .
            '.php';

        if (is_file($appFile)) {
            require_once $appFile;
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | MODULE Namespace
        |--------------------------------------------------------------------------
        */

        $moduleFile = self::resolveModuleFile($relative);

        if ($moduleFile !== null && is_file($moduleFile)) {
            require_once $moduleFile;
        }
    }

    /**
     * Resolve a class relative to the Fandoogh namespace to a module file.
     */
    private static function resolveModuleFile(string $relative): ?string
    {
        if (! str_starts_with($relative, self::MODULE_NAMESPACE)) {
            return null;
        }

        $module = substr($relative, strlen(self::MODULE_NAMESPACE));
        $parts = explode('\\', $module);

        if (count($parts) < 2) {
            return null;
        }

        $namespace = array_shift($parts);
        $folder = self::MODULE_DIRECTORIES[$namespace]
            ?? strtolower($namespace);

        return FA_PATH .
            'modules/' .
            $folder .
            '/' .
            implode(DIRECTORY_SEPARATOR, $parts) .
            '.php';
    }
}
