<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

if (! function_exists('fa')) {

    /**
     * Get the Fandoogh application instance.
     *
     * @return \Fandoogh\Core\Application
     */
    function fa(): \Fandoogh\Core\Application
    {
        return \Fandoogh\Core\Application::instance();
    }
}

if (! function_exists('get_active_fixed_prices_for_product')) {
    /**
     * Return active fixed-price rows mapped to a WooCommerce product.
     */
    function get_active_fixed_prices_for_product(int $product_id): array
    {
        return \Fandoogh\Calculator\FixedPriceService::activeForProduct(absint($product_id));
    }
}
