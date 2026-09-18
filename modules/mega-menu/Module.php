<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Module
{
    public function boot(): void
    {
        (new Frontend())->boot();
        (new Shortcode())->boot();
        (new MobileShortcode())->boot();

    }
}
