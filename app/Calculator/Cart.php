<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class Cart
{
    private const DATA_KEY = 'fa_fandoogh_calculator';

    public function boot(): void
    {
        add_action('wp_ajax_fandoogh_calculator_add_to_cart', [$this, 'addToCart']);
        add_action('wp_ajax_nopriv_fandoogh_calculator_add_to_cart', [$this, 'addToCart']);
        add_action('woocommerce_before_calculate_totals', [$this, 'applyCalculatedPrice'], 20);
        add_filter('woocommerce_get_item_data', [$this, 'displayItemData'], 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'saveOrderItemData'], 10, 4);
    }

    public function addToCart(): void
    {
        if (! check_ajax_referer('fa_fandoogh_calculator', 'nonce', false)) {
            wp_send_json_error(['message' => 'اعتبار درخواست منقضی شده است؛ صفحه را تازه‌سازی کنید.'], 403);
        }

        $selectedOptionalFees = isset($_POST['optional_fees']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['optional_fees'])) : [];
        $quote = QuoteService::calculate(
            absint($_POST['product_id'] ?? 0),
            absint($_POST['variation_id'] ?? 0),
            wp_unslash($_POST['meters'] ?? 0),
            $selectedOptionalFees
        );

        if (is_wp_error($quote)) {
            wp_send_json_error(['message' => $quote->get_error_message()], 400);
        }

        if (function_exists('wc_load_cart') && (! WC()->cart instanceof \WC_Cart)) {
            wc_load_cart();
        }

        if (! WC()->cart instanceof \WC_Cart) {
            wp_send_json_error(['message' => 'سبد خرید ووکامرس در دسترس نیست.'], 500);
        }

        $cartItemKey = WC()->cart->add_to_cart(
            $quote['product_id'],
            1,
            $quote['variation_id'],
            $quote['variation_attributes'],
            [self::DATA_KEY => $quote]
        );

        if (! is_string($cartItemKey) || $cartItemKey === '') {
            wp_send_json_error(['message' => 'افزودن پیش‌فاکتور به سبد خرید انجام نشد.'], 400);
        }

        wp_send_json_success([
            'message' => 'پیش‌فاکتور با موفقیت به سبد خرید اضافه شد.',
            'cart_url' => wc_get_cart_url(),
            'cart_item_key' => $cartItemKey,
        ]);
    }

    public function applyCalculatedPrice(\WC_Cart $cart): void
    {
        foreach ($cart->get_cart() as $cartItem) {
            $quote = $cartItem[self::DATA_KEY] ?? null;
            if (! is_array($quote) || ! isset($quote['total']) || ! $cartItem['data'] instanceof \WC_Product) {
                continue;
            }

            $cartItem['data']->set_price(Currency::fromToman((float) $quote['total']));
        }
    }

    public function displayItemData(array $itemData, array $cartItem): array
    {
        $quote = $cartItem[self::DATA_KEY] ?? null;
        if (! is_array($quote)) {
            return $itemData;
        }

        $itemData[] = ['key' => 'طول دیوار', 'value' => self::number((float) $quote['meters']) . ' متر'];
        $itemData[] = ['key' => 'قیمت هر متر', 'value' => self::money((float) $quote['price_per_meter'])];

        foreach ((array) ($quote['fixed_prices'] ?? []) as $fee) {
            if (! is_array($fee)) {
                continue;
            }
            $suffix = ($fee['type'] ?? '') === FixedPriceService::PER_METER ? ' / متر' : ' مقطوع';
            $itemData[] = [
                'key' => sanitize_text_field((string) ($fee['title'] ?? 'هزینه ثابت')),
                'value' => self::money((float) ($fee['price'] ?? 0)) . $suffix,
            ];
        }

        return $itemData;
    }

    public function saveOrderItemData(\WC_Order_Item_Product $item, string $cartItemKey, array $values, \WC_Order $order): void
    {
        unset($cartItemKey, $order);
        $quote = $values[self::DATA_KEY] ?? null;
        if (! is_array($quote)) {
            return;
        }

        $item->add_meta_data('طول دیوار', self::number((float) $quote['meters']) . ' متر', true);
        $item->add_meta_data('قیمت محاسبه‌شده هر متر', self::money((float) $quote['price_per_meter']), true);

        foreach ((array) ($quote['fixed_prices'] ?? []) as $fee) {
            if (is_array($fee) && ! empty($fee['title'])) {
                $item->add_meta_data(
                    sanitize_text_field((string) $fee['title']),
                    self::money((float) ($fee['price'] ?? 0)),
                    false
                );
            }
        }
    }

    private static function money(float $amount): string
    {
        return number_format_i18n($amount, 0) . ' تومان';
    }

    private static function number(float $number): string
    {
        return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
    }
}
