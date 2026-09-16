<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('FA_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('FA_APP', FA_PATH . 'app' . DIRECTORY_SEPARATOR);

require_once FA_APP . 'Core/Autoloader.php';

\Fandoogh\Core\Autoloader::register();

$classes = [
    \Fandoogh\Core\JalaliDate::class,
    \Fandoogh\Modules\Reviews\RateLimiter::class,
    \Fandoogh\Modules\MegaMenu\Module::class,
];

foreach ($classes as $class) {
    if (! class_exists($class)) {
        fwrite(STDERR, "Failed to autoload {$class}" . PHP_EOL);
        exit(1);
    }
}

echo "Autoloader test passed." . PHP_EOL;
