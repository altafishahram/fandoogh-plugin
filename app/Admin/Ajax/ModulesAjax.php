<?php

declare(strict_types=1);

namespace Fandoogh\Admin\Ajax;

use Fandoogh\Core\Application;
use Fandoogh\Managers\ModuleManager;

defined('ABSPATH') || exit;

final class ModulesAjax
{
    public function boot(): void
    {
        add_action('wp_ajax_fa_toggle_module', [$this, 'toggle']);
    }

    public function toggle(): void
    {
        if (! check_ajax_referer('fa_modules', 'nonce', false)) {
            wp_send_json_error(['message' => __('اعتبار درخواست منقضی شده است.', 'fandoogh')], 403);
        }
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('دسترسی غیرمجاز است.', 'fandoogh')], 403);
        }

        $module = sanitize_key(wp_unslash($_POST['module'] ?? ''));
        if ($module === '') {
            wp_send_json_error(['message' => __('ماژول نامعتبر است.', 'fandoogh')], 400);
        }

        $modules = Application::instance()->get('modules');
        if (! $modules instanceof ModuleManager) {
            wp_send_json_error(['message' => __('مدیر ماژول‌ها پیدا نشد.', 'fandoogh')], 500);
        }
        if (! array_key_exists($module, $modules->registry())) {
            wp_send_json_error(['message' => __('ماژول انتخاب‌شده معتبر نیست.', 'fandoogh')], 400);
        }

        $modules->toggle($module);
        wp_send_json_success([
            'module' => $module,
            'status' => $modules->enabled($module),
            'message' => __('وضعیت ماژول با موفقیت تغییر کرد.', 'fandoogh'),
        ]);
    }
}
