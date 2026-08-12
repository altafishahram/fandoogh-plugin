<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

defined('ABSPATH') || exit;

final class Admin
{
    public function boot(): void
    {
        add_action(
            'admin_enqueue_scripts',
            [$this, 'enqueueAssets']
        );
    }

    public function enqueueAssets(): void
    {
        $screen = get_current_screen();

        if (
            !$screen ||
            $screen->post_type !== 'fa_customer'
        ) {
            return;
        }

        // Media Library
        wp_enqueue_media();

        // WordPress visual/text editor.
        wp_enqueue_editor();

        // Javascript
        wp_enqueue_script(
            'fa-customer-admin',
            FA_URL . 'assets/admin/js/customer-admin.js',
            ['jquery'],
            FA_VERSION,
            true
        );

        // CSS
        wp_enqueue_style(
            'fa-customer-admin',
            FA_URL . 'assets/admin/css/customer-admin.css',
            [],
            FA_VERSION
        );
    }
}
