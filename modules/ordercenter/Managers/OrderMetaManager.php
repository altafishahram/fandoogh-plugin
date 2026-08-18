<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Managers;

defined('ABSPATH') || exit;

final class OrderMetaManager
{
    private const DENY_KEYS = ['_order_key', '_customer_user', '_created_via', '_order_version', '_payment_method', '_payment_method_title', '_transaction_id', '_billing_first_name', '_billing_last_name', '_billing_email', '_billing_phone', '_shipping_first_name', '_shipping_last_name', '_shipping_phone'];

    public function visible(\WC_Order $order): array
    {
        $result = [];
        $allowList = array_values(array_filter(array_map('sanitize_key', (array) apply_filters('fandoogh_order_center_visible_meta_keys', []))));
        foreach ($order->get_meta_data() as $meta) {
            $key = (string) $meta->key;
            if ($key === '' || ($allowList !== [] && ! in_array($key, $allowList, true)) || str_starts_with($key, '_') || in_array($key, self::DENY_KEYS, true) || $this->isSensitive($key)) { continue; }
            $value = $meta->value;
            if (is_array($value) || is_object($value)) { $value = wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
            if (! is_scalar($value)) { continue; }
            $value = wp_html_excerpt(sanitize_text_field((string) $value), 300, '…');
            if ($value !== '') { $result[] = ['key' => $key, 'value' => $value]; }
        }
        return $result;
    }

    public function tracking(\WC_Order $order): array
    {
        $items = $order->get_meta('_wc_shipment_tracking_items', true);
        if (! is_array($items)) {
            $items = [];
            foreach (['_tracking_number', 'tracking_number', '_shipment_tracking_number'] as $key) {
                $number = $order->get_meta($key, true);
                if (is_scalar($number) && (string) $number !== '') { $items[] = ['tracking_number' => (string) $number, 'tracking_provider' => '', 'custom_tracking_link' => '']; }
            }
        }
        $result = [];
        foreach ($items as $item) {
            if (! is_array($item)) { continue; }
            $number = $item['tracking_number'] ?? '';
            if (! is_scalar($number) || trim((string) $number) === '') { continue; }
            $provider = $item['tracking_provider'] ?? ($item['custom_tracking_provider'] ?? '');
            $url = $item['custom_tracking_link'] ?? ($item['tracking_link'] ?? '');
            $result[] = ['provider' => is_scalar($provider) ? sanitize_text_field((string) $provider) : '', 'number' => sanitize_text_field((string) $number), 'url' => is_scalar($url) ? esc_url_raw((string) $url) : ''];
        }
        return $result;
    }

    private function isSensitive(string $key): bool
    {
        return (bool) preg_match('/token|secret|password|passwd|api[_-]?key|authorization|card|cvv|security/i', $key);
    }
}
