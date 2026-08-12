<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class Api
{
    public function boot(): void
    {
        add_action('wp_ajax_get_product_variations_and_fixed_prices', [$this, 'productData']);
        add_action('wp_ajax_nopriv_get_product_variations_and_fixed_prices', [$this, 'productData']);
    }

    public function productData(): void
    {
        if (! check_ajax_referer('fa_fandoogh_calculator', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است؛ صفحه را تازه‌سازی کنید.'], 403);
        }

        $productId = absint($_POST['product_id'] ?? 0);
        $data = Catalog::productData($productId);

        if (is_wp_error($data)) {
            wp_send_json_error(['message' => $data->get_error_message()], 400);
        }

        wp_send_json_success($data);
    }
}
