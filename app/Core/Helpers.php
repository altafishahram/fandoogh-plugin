<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

if (! function_exists('fa')) {

    /**
     * Get the Fandoogh application instance.
     *
     * @return \Fandoogh\Core\Application
     */
    function fa(): \Fandoogh\Core\Application
    {
        return \Fandoogh\Core\Application::instance();
    }
}
