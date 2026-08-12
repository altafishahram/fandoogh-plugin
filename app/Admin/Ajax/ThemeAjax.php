<?php

declare(strict_types=1);

namespace Fandoogh\Admin\Ajax;

use Fandoogh\AdminTheme\SettingsSchema;
use Fandoogh\AdminTheme\ThemeManager;

defined('ABSPATH') || exit;

final class ThemeAjax
{
    public function boot(): void
    {
        add_action('wp_ajax_fa_save_admin_theme', [$this, 'save']);
    }

    public function save(): void
    {
        if (! check_ajax_referer('fa_admin', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است.'], 403);
        }
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز است.'], 403);
        }

        $raw = isset($_POST['settings']) && is_array($_POST['settings'])
            ? wp_unslash($_POST['settings']) : [];
        if (! empty($_POST['reset'])) {
            $raw = SettingsSchema::defaults();
        }

        $result = (new ThemeManager())->save($raw);
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()], 500);
        }

        wp_send_json_success([
            'message' => 'پوسته پنل با موفقیت ذخیره و فایل CSS جدید تولید شد.',
            'settings' => $result['settings'],
            'version' => $result['asset']['version'],
            'cssUrl' => $result['asset']['url'] ?? '',
        ]);
    }
}
