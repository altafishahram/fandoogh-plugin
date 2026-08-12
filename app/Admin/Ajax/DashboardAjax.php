<?php

declare(strict_types=1);

namespace Fandoogh\Admin\Ajax;

use Fandoogh\Admin\Sections;

defined('ABSPATH') || exit;

final class DashboardAjax
{
    public function boot(): void
    {
        add_action('wp_ajax_fa_load_admin_section', [$this, 'load']);
    }

    public function load(): void
    {
        if (! check_ajax_referer('fa_admin', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است.'], 403);
        }
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز است.'], 403);
        }

        $section = sanitize_key(wp_unslash($_POST['section'] ?? 'dashboard'));
        if (! in_array($section, ['dashboard', 'modules', 'product_seo', 'calculator', 'crm', 'theme', 'settings', 'support'], true)) {
            $section = 'dashboard';
        }

        wp_send_json_success(['html' => Sections::render($section), 'section' => $section]);
    }
}
