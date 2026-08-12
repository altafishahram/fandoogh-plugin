<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

/**
 * FAQ Module.
 *
 * Boots the FAQ module.
 *
 * @package Fandoogh\Modules\Faq
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
