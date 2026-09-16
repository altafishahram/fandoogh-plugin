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
        add_action('admin_post_fa_toggle_module', [$this, 'togglePost']);
    }

    public function toggle(): void
    {
        if (! check_ajax_referer('fa_modules', 'nonce', false)) {
            wp_send_json_error(['message' => __('اعتبار درخواست منقضی شده است.', 'fandoogh')], 403);
        }
        $result = $this->change();
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()], (int) ($result->get_error_data()['status'] ?? 400));
        }
        wp_send_json_success([
            'module' => $result['module'],
            'status' => $result['status'],
            'message' => __('وضعیت ماژول با موفقیت تغییر کرد.', 'fandoogh'),
        ]);
    }

    public function togglePost(): void
    {
        check_admin_referer('fa_modules', 'nonce');
        $result = $this->change();
        if (is_wp_error($result)) {
            wp_die(
                esc_html($result->get_error_message()),
                esc_html__('تغییر وضعیت ماژول', 'fandoogh'),
                ['response' => (int) ($result->get_error_data()['status'] ?? 400)]
            );
        }

        $fallback = admin_url('admin.php?page=fa-modules');
        $referer = wp_get_referer();
        $redirect = is_string($referer) && str_starts_with($referer, admin_url()) ? $referer : $fallback;
        wp_safe_redirect(add_query_arg('fa_module_changed', $result['status'] ? 'enabled' : 'disabled', $redirect));
        exit;
    }

    /** @return array{module:string,status:bool}|\WP_Error */
    private function change(): array|\WP_Error
    {
        if (! current_user_can('manage_options')) {
            return new \WP_Error('fa_module_forbidden', __('دسترسی غیرمجاز است.', 'fandoogh'), ['status' => 403]);
        }

        $module = sanitize_key(wp_unslash($_POST['module'] ?? ''));
        if ($module === '') {
            return new \WP_Error('fa_module_invalid', __('ماژول نامعتبر است.', 'fandoogh'), ['status' => 400]);
        }

        $modules = Application::instance()->get('modules');
        if (! $modules instanceof ModuleManager) {
            return new \WP_Error('fa_module_manager', __('مدیر ماژول‌ها پیدا نشد.', 'fandoogh'), ['status' => 500]);
        }
        if (! array_key_exists($module, $modules->registry())) {
            return new \WP_Error('fa_module_unknown', __('ماژول انتخاب‌شده معتبر نیست.', 'fandoogh'), ['status' => 400]);
        }

        $modules->toggle($module);
        return ['module' => $module, 'status' => $modules->enabled($module)];
    }
}
