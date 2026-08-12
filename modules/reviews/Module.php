<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

defined('ABSPATH') || exit;

/**
 * Reviews Module.
 *
 * Boots the Reviews module.
 *
 * @package Fandoogh\Modules\Reviews
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

        (new PostType())->boot();

        (new Sync())->boot();

        (new Cache())->boot();

        (new Ajax())->boot();

        (new Frontend())->boot();

    }


}
