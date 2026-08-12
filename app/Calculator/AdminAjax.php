<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class AdminAjax
{
    public function boot(): void
    {
        add_action('wp_ajax_fa_save_fixed_prices', [$this, 'save']);
    }

    public function save(): void
    {
        if (! check_ajax_referer('fa_calculator_admin', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است.'], 403);
        }

        if (! current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => 'اجازه مدیریت قیمت‌های ماشین حساب را ندارید.'], 403);
        }

        $items = isset($_POST['fixed_prices'])
            ? wp_unslash($_POST['fixed_prices'])
            : [];
        $items = FixedPriceService::save(is_array($items) ? $items : []);

        $settings = isset($_POST['settings'])
            ? wp_unslash($_POST['settings'])
            : [];
        $settings = SettingsService::save(is_array($settings) ? $settings : []);

        wp_send_json_success([
            'message' => 'تنظیمات و قیمت‌های ثابت با موفقیت ذخیره شدند.',
            'items' => $items,
            'settings' => $settings,
        ]);
    }
}
