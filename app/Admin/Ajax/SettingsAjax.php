<?php

declare(strict_types=1);

namespace Fandoogh\Admin\Ajax;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class SettingsAjax
{
    public function boot(): void
    {
        add_action('wp_ajax_fa_save_settings', [$this, 'save']);
    }

    public function save(): void
    {
        if (! check_ajax_referer('fa_admin', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است.'], 403);
        }
        if (! current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز است.'], 403);
        }

        $deleteData = isset($_POST['delete_data']) && sanitize_key(wp_unslash($_POST['delete_data'])) === '1';
        update_option(Options::DELETE_DATA_ON_UNINSTALL, $deleteData, false);
        wp_send_json_success(['message' => 'تنظیمات با موفقیت ذخیره شد.']);
    }
}
