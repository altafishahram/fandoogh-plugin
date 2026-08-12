<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

defined('ABSPATH') || exit;

final class Frontend
{
    public function boot(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'fa-customer',
            FA_URL . 'assets/frontend/css/customer.css',
            [],
            FA_VERSION
        );
    }
}
