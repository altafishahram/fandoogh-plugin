<?php

declare(strict_types=1);

namespace Fandoogh\Projects;

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
            $screen->post_type !== 'fa_project'
        ) {
            return;
        }

        // Media Library
        wp_enqueue_media();

        wp_enqueue_editor();

        // Javascript
        wp_enqueue_script(
            'fa-project-admin',
            FA_URL . 'assets/admin/js/project-admin.js',
            ['jquery'],
            FA_VERSION,
            true
        );

        // CSS
        wp_enqueue_style(
            'fa-project-admin',
            FA_URL . 'assets/admin/css/project-admin.css',
            [],
            FA_VERSION
        );
    }
}
