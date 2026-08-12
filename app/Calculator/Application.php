<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class Application
{
    public function boot(): void
    {
        (new Api())->boot();
        (new Cart())->boot();
        (new Shortcode())->boot();

        if (is_admin()) {
            (new AdminAjax())->boot();
        }
    }
}
