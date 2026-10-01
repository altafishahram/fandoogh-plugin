<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

defined('ABSPATH') || exit;

final class Display
{
    public static function assets(): void
    {
        wp_enqueue_style('fa-partner-price', FA_URL . 'assets/frontend/css/partner-price.css', [], FA_BUILD);
    }

    public static function shortcode(array|string $attributes = []): string
    {
        $args = shortcode_atts([
            'product_id' => 0,
            'label' => __('قیمت همکاری', 'fandoogh'),
            'show_cart' => 'no',
        ], (array) $attributes, 'fa_partner_price');
        return self::render($args);
    }

    public static function render(array $args = []): string
    {
        // Visibility is never overridden by a widget, shortcode argument or preview.
        if (! Pricing::active()) {
            return '';
        }
        $id = absint($args['product_id'] ?? 0);
        if ($id === 0) {
            global $product;
            $id = $product instanceof \WC_Product ? $product->get_id() : get_queried_object_id();
        }
        $item = wc_get_product($id);
        if (! $item || ! $item->is_type(['simple', 'variable', 'variation']) || $item->get_status() !== 'publish') {
            return '';
        }
        $publicId = $item->is_type('variation') ? $item->get_parent_id() : $id;
        if (get_post_status($publicId) !== 'publish' || post_password_required($publicId)) {
            return '';
        }
        $html = $item->get_price_html();
        if ($html === '') {
            return '';
        }
        $label = sanitize_text_field((string) ($args['label'] ?? __('قیمت همکاری', 'fandoogh')));
        $result = '<div class="fa-partner-price" data-product-id="' . esc_attr((string) $id) . '">';
        if ($label !== '') {
            $result .= '<span class="fa-partner-price__label">' . esc_html($label) . '</span>';
        }
        $result .= '<div class="fa-partner-price__amount">' . wp_kses_post($html) . '</div>';
        if (($args['show_cart'] ?? 'no') === 'yes' && $item->is_purchasable() && $item->is_in_stock()) {
            $result .= '<a class="fa-partner-price__buy" href="' . esc_url($item->add_to_cart_url()) . '" rel="nofollow">' . esc_html($item->add_to_cart_text()) . '</a>';
        }
        return $result . '</div>';
    }
}
