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

        if (str_starts_with($relative, self::MODULE_NAMESPACE)) {

            $module = substr($relative, strlen(self::MODULE_NAMESPACE));

            $parts = explode('\\', $module);

            if (count($parts) < 2) {
                return;
            }

            $folder = strtolower(array_shift($parts));

            $file = FA_PATH .
                'modules/' .
                $folder .
                '/' .
                implode(DIRECTORY_SEPARATOR, $parts) .
                '.php';

            if (is_file($file)) {
                require_once $file;
            }
        }
    }
}