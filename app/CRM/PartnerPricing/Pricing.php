<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

defined('ABSPATH') || exit;

final class Pricing
{
    public const PRICE_META = '_fa_partner_price';

    public function boot(): void
    {
        foreach (['price', 'regular_price', 'sale_price'] as $field) {
            add_filter('woocommerce_product_get_' . $field, [$this, 'price'], 99, 2);
            add_filter('woocommerce_product_variation_get_' . $field, [$this, 'price'], 99, 2);
            add_filter('woocommerce_variation_prices_' . $field, [$this, 'price'], 99, 2);
        }
        add_filter('woocommerce_get_variation_prices_hash', [$this, 'cacheHash'], 99, 1);
        add_action('woocommerce_update_product_variation', [$this, 'clearVariationCache'], 10, 1);
        add_action('template_redirect', [$this, 'privatePage']);
    }

    public static function active(): bool
    {
        // Administrative editing always sees the real stored retail prices.
        return (! is_admin() || wp_doing_ajax()) && Access::isPartner();
    }

    /** Empty is unset; zero is a valid explicitly configured price. */
    public static function amount(\WC_Product $product): ?string
    {
        if (! $product->is_type(['simple', 'variation'])) {
            return null;
        }
        $value = $product->get_meta(self::PRICE_META, true, 'edit');
        if (! is_scalar($value) || $value === '' || ! is_numeric($value) || ! is_finite((float) $value) || (float) $value < 0) {
            return null;
        }
        return (string) $value;
    }

    public function price($price, \WC_Product $product)
    {
        if (! self::active() || $product->get_price('edit') === '') {
            return $price;
        }
        // WooCommerce uses these getters for catalog, cart, checkout and Store API totals.
        // Never write the private price to _price or trust a price submitted by a buyer.
        return self::amount($product) ?? $price;
    }

    public function cacheHash(array $hash): array
    {
        $hash['fa_partner_pricing'] = self::active() ? 'partner' : 'retail';
        return $hash;
    }

    public function clearVariationCache(int $id): void
    {
        $product = wc_get_product($id);
        if ($product && $product->get_parent_id() > 0) {
            wc_delete_product_transients($product->get_parent_id());
        }
    }

    public function privatePage(): void
    {
        if (Access::isPartner()) {
            if (! defined('DONOTCACHEPAGE')) {
                define('DONOTCACHEPAGE', true);
            }
            nocache_headers();
        }
    }
}
