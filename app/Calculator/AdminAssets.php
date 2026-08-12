<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

use Fandoogh\Core\Constants\Assets;

defined('ABSPATH') || exit;

final class AdminAssets
{
    public function enqueue(): void
    {
        wp_enqueue_style('woocommerce_admin_styles');
        wp_enqueue_script('wc-enhanced-select');
        wp_enqueue_style(
            Assets::CALCULATOR_ADMIN,
            FA_URL . Assets::CALCULATOR_ADMIN_CSS,
            [Assets::ADMIN],
            FA_BUILD
        );
        wp_enqueue_script(
            Assets::CALCULATOR_ADMIN,
            FA_URL . Assets::CALCULATOR_ADMIN_JS,
            ['jquery', 'wc-enhanced-select'],
            FA_BUILD,
            true
        );
        wp_localize_script(Assets::CALCULATOR_ADMIN, 'faCalculatorAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fa_calculator_admin'),
            'removeConfirm' => 'این قیمت ثابت حذف شود؟',
        ]);
    }
}
