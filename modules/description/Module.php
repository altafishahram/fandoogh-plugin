<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Description;

defined('ABSPATH') || exit;

/**
 * Description Module
 *
 * Boots the Description module.
 *
 * @package Fandoogh\Modules\Description
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
