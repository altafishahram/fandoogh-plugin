<?php

declare(strict_types=1);

namespace Fandoogh\Admin\Ajax;

use Fandoogh\Admin\Sections;
use Fandoogh\Core\Application;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\Modules\OrderCenter\Module as OrderCenterModule;

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
        if (! in_array($section, ['dashboard', 'modules', 'product_seo', 'order_center', 'calculator', 'crm', 'theme', 'wp_dashboard', 'wp_login', 'settings', 'support'], true)) {
            $section = 'dashboard';
        }

        if ($section === 'order_center') {
            $modules = Application::instance()->get('modules');
            if (! $modules instanceof ModuleManager || ! $modules->enabled('order-center') || ! OrderCenterModule::isAvailable()) {
                wp_send_json_error(['message' => 'ماژول مرکز سفارشات فعال نیست یا ووکامرس در دسترس نیست.'], 400);
            }
        }

        wp_send_json_success(['html' => Sections::render($section), 'section' => $section]);
    }
}
