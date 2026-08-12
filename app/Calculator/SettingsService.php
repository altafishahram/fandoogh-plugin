<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class SettingsService
{
    public static function get(): array
    {
        return self::sanitize(SettingsRepository::get());
    }

    public static function save(array $settings): array
    {
        $clean = self::sanitize($settings);
        SettingsRepository::save($clean);

        return $clean;
    }

    public static function sanitize(array $settings): array
    {
        $defaults = [
            'opacity' => 97, // 0 to 100
            'color_primary' => '#fb923c',
            'color_bg' => '#1b4332',
            'color_text' => '#ffffff',
            'cta_action' => 'woocommerce_cart', // woocommerce_cart, contact_direct, scroll_to_form
            'cta_target' => '',
            'label_quantity' => 'متراژ (متر)',
            'label_unit' => 'متر',
            'label_mandatory_fees' => 'هزینه‌های لحاظ‌شده',
            'label_submit' => 'ثبت پیش‌فاکتور',
            'allowed_categories' => [], // Array of category IDs
            'message_template' => "سلام، من درخواست پیش‌فاکتور برای محصول {product} دارم.\nمقدار: {quantity} {unit}\nمبلغ کل: {total}",
        ];

        $opacity = isset($settings['opacity']) ? (int) $settings['opacity'] : $defaults['opacity'];
        $opacity = max(0, min(100, $opacity));

        $ctaAction = sanitize_key((string) ($settings['cta_action'] ?? $defaults['cta_action']));
        if (!in_array($ctaAction, ['woocommerce_cart', 'contact_direct', 'scroll_to_form'], true)) {
            $ctaAction = 'woocommerce_cart';
        }

        $allowedCategories = array_values(array_unique(array_filter(
            array_map('absint', (array) ($settings['allowed_categories'] ?? [])),
            static fn(int $id): bool => $id > 0
        )));

        return [
            'opacity' => $opacity,
            'color_primary' => sanitize_hex_color((string) ($settings['color_primary'] ?? $defaults['color_primary'])) ?: $defaults['color_primary'],
            'color_bg' => sanitize_hex_color((string) ($settings['color_bg'] ?? $defaults['color_bg'])) ?: $defaults['color_bg'],
            'color_text' => sanitize_hex_color((string) ($settings['color_text'] ?? $defaults['color_text'])) ?: $defaults['color_text'],
            'cta_action' => $ctaAction,
            'cta_target' => sanitize_text_field((string) ($settings['cta_target'] ?? '')),
            'label_quantity' => sanitize_text_field((string) ($settings['label_quantity'] ?? $defaults['label_quantity'])) ?: $defaults['label_quantity'],
            'label_unit' => sanitize_text_field((string) ($settings['label_unit'] ?? $defaults['label_unit'])) ?: $defaults['label_unit'],
            'label_mandatory_fees' => sanitize_text_field((string) ($settings['label_mandatory_fees'] ?? $defaults['label_mandatory_fees'])) ?: $defaults['label_mandatory_fees'],
            'label_submit' => sanitize_text_field((string) ($settings['label_submit'] ?? $defaults['label_submit'])) ?: $defaults['label_submit'],
            'allowed_categories' => $allowedCategories,
            'message_template' => sanitize_textarea_field((string) ($settings['message_template'] ?? $defaults['message_template'])) ?: $defaults['message_template'],
        ];
    }
}
