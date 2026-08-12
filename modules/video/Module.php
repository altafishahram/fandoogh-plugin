<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Video;

defined('ABSPATH') || exit;

/**
 * Video Module
 *
 * Boots the Video module.
 *
 * @package Fandoogh\Modules\Video
 */
final class Module
{
    /**
     * Boot module.
     *
     * @return void
     */
    public function boot(): void
    {
        if (is_admin()) {
            (new Admin())->boot();
        }

        (new Frontend())->boot();

    }

}
