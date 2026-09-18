<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Service
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'button_text' => __('دسته‌بندی محصولات', 'fandoogh'),
            'button_icon' => 'dashicons-screenoptions',
            'interaction_mode' => 'hover',
            'styles' => [
                'button_bg' => '#6200ea', 'button_color' => '#ffffff', 'button_hover' => '#7c4dff', 'button_active' => '#7c4dff',
                'button_radius' => 8, 'button_padding' => 10, 'button_font_size' => 15, 'button_shadow' => '0 4px 12px rgba(0,0,0,.2)',
                'menu_width' => 920, 'sidebar_width' => 250, 'menu_radius' => 12,
                'menu_shadow' => '0 12px 40px rgba(0,0,0,.13)', 'menu_bg' => '#ffffff',
                'sidebar_bg' => '#f8f5ff', 'sidebar_color' => '#424242', 'sidebar_hover' => '#6200ea', 'sidebar_hover_bg' => '#ffffff',
                'sidebar_active' => '#6200ea', 'sidebar_active_bg' => '#ffffff', 'sidebar_font_size' => 14, 'sidebar_padding' => 14,
                'grid_columns' => 3, 'card_border' => '#eeeeee', 'card_hover_border' => '#d1c4e9',
                'card_radius' => 10, 'card_hover_shadow' => '0 8px 22px rgba(98,0,234,.12)', 'card_icon_bg' => '#f3e5f5',
                'card_title_size' => 14, 'card_count_size' => 12, 'image_size' => 42,
                'banner_height' => 110, 'banner_radius' => 10,
                'banner_background' => 'linear-gradient(135deg,#7c4dff,#6200ea)',
            ],
            'banners' => [],
        ];
    }

    public static function settings(): array
    {
        return self::merge(self::defaults(), Repository::settings());
    }

    public static function sanitize(array $input): array
    {
        $defaults = self::defaults();
        $styles = [];
        $rawStyles = is_array($input['styles'] ?? null) ? $input['styles'] : [];
        $colorKeys = ['button_bg', 'button_color', 'button_hover', 'button_active', 'menu_bg', 'sidebar_bg', 'sidebar_color', 'sidebar_hover', 'sidebar_hover_bg', 'sidebar_active', 'sidebar_active_bg', 'card_border', 'card_hover_border', 'card_icon_bg'];
        $intKeys = ['button_radius', 'button_padding', 'button_font_size', 'menu_width', 'sidebar_width', 'menu_radius', 'sidebar_font_size', 'sidebar_padding', 'grid_columns', 'card_radius', 'card_title_size', 'card_count_size', 'image_size', 'banner_height', 'banner_radius'];
        foreach ($defaults['styles'] as $key => $default) {
            if (in_array($key, $colorKeys, true)) {
                $styles[$key] = sanitize_hex_color((string) ($rawStyles[$key] ?? '')) ?: $default;
            } elseif (in_array($key, $intKeys, true)) {
                $styles[$key] = absint($rawStyles[$key] ?? $default);
            } else {
                $styles[$key] = self::safeCssValue((string) ($rawStyles[$key] ?? ''), (string) $default);
            }
        }
        $styles['grid_columns'] = min(4, max(2, (int) $styles['grid_columns']));
        $styles['menu_width'] = min(1600, max(320, (int) $styles['menu_width']));
        $styles['sidebar_width'] = min(500, max(130, (int) $styles['sidebar_width']));
        $banners = [];
        foreach ((array) ($input['banners'] ?? []) as $termId => $banner) {
            if (! is_array($banner) || ! term_exists((int) $termId, 'product_cat')) continue;
            $type = in_array($banner['type'] ?? '', ['image', 'html', 'text'], true) ? $banner['type'] : 'text';
            $banners[(int) $termId] = [
                'type' => $type, 'image_id' => self::attachmentId($banner['image_id'] ?? 0),
                'html' => wp_kses_post((string) ($banner['html'] ?? '')),
                'title' => sanitize_text_field((string) ($banner['title'] ?? '')),
                'text' => sanitize_textarea_field((string) ($banner['text'] ?? '')),
                'button_text' => sanitize_text_field((string) ($banner['button_text'] ?? '')),
                'button_url' => esc_url_raw((string) ($banner['button_url'] ?? '')),
            ];
        }
        $icon = sanitize_key((string) ($input['button_icon'] ?? $defaults['button_icon']));
        if (! array_key_exists($icon, self::buttonIcons())) {
            $icon = $defaults['button_icon'];
        }

        $interaction = in_array($input['interaction_mode'] ?? '', ['hover', 'click'], true) ? $input['interaction_mode'] : $defaults['interaction_mode'];

        return ['button_text' => sanitize_text_field((string) ($input['button_text'] ?? $defaults['button_text'])), 'button_icon' => $icon, 'interaction_mode' => $interaction, 'styles' => $styles, 'banners' => $banners];
    }

    /** @return array<string, string> */
    public static function buttonIcons(): array
    {
        return [
            'dashicons-screenoptions' => __('شبکه دسته‌بندی', 'fandoogh'),
            'dashicons-menu-alt3' => __('منو', 'fandoogh'),
            'dashicons-category' => __('دسته‌بندی', 'fandoogh'),
            'dashicons-products' => __('محصولات', 'fandoogh'),
            'dashicons-cart' => __('سبد خرید', 'fandoogh'),
            'dashicons-store' => __('فروشگاه', 'fandoogh'),
            'dashicons-list-view' => __('فهرست', 'fandoogh'),
            'dashicons-tag' => __('برچسب', 'fandoogh'),
        ];
    }

    public static function cssVariables(array $settings, array $overrides = []): string
    {
        $style = array_merge($settings['styles'], $overrides);
        $map = [
            'button_bg' => '--fa-mega-button-bg', 'button_color' => '--fa-mega-button-color', 'button_hover' => '--fa-mega-button-hover', 'button_active' => '--fa-mega-button-active',
            'button_radius' => '--fa-mega-button-radius', 'button_padding' => '--fa-mega-button-padding', 'button_font_size' => '--fa-mega-button-font-size', 'button_shadow' => '--fa-mega-button-shadow',
            'menu_width' => '--fa-mega-width', 'sidebar_width' => '--fa-mega-sidebar-width', 'menu_radius' => '--fa-mega-radius', 'menu_shadow' => '--fa-mega-shadow', 'menu_bg' => '--fa-mega-menu-bg',
            'sidebar_bg' => '--fa-mega-sidebar-bg', 'sidebar_color' => '--fa-mega-sidebar-color', 'sidebar_hover' => '--fa-mega-sidebar-hover', 'sidebar_hover_bg' => '--fa-mega-sidebar-hover-bg', 'sidebar_active' => '--fa-mega-sidebar-active', 'sidebar_active_bg' => '--fa-mega-sidebar-active-bg', 'sidebar_font_size' => '--fa-mega-sidebar-font-size', 'sidebar_padding' => '--fa-mega-sidebar-padding',
            'grid_columns' => '--fa-mega-grid-columns', 'card_border' => '--fa-mega-card-border', 'card_hover_border' => '--fa-mega-card-hover-border', 'card_radius' => '--fa-mega-card-radius', 'card_hover_shadow' => '--fa-mega-card-hover-shadow', 'card_icon_bg' => '--fa-mega-card-icon-bg', 'card_title_size' => '--fa-mega-card-title-size', 'card_count_size' => '--fa-mega-card-count-size', 'image_size' => '--fa-mega-image-size', 'banner_height' => '--fa-mega-banner-height', 'banner_radius' => '--fa-mega-banner-radius', 'banner_background' => '--fa-mega-banner-bg',
        ];
        $px = ['button_radius', 'button_padding', 'button_font_size', 'menu_width', 'sidebar_width', 'menu_radius', 'sidebar_font_size', 'sidebar_padding', 'card_radius', 'card_title_size', 'card_count_size', 'image_size', 'banner_height', 'banner_radius'];
        $css = [];
        foreach ($map as $key => $var) if (isset($style[$key])) $css[] = $var . ':' . esc_attr((string) $style[$key] . (in_array($key, $px, true) ? 'px' : ''));
        return implode(';', $css);
    }

    private static function merge(array $defaults, array $saved): array
    {
        $defaults['styles'] = array_merge($defaults['styles'], is_array($saved['styles'] ?? null) ? $saved['styles'] : []);
        $defaults['banners'] = is_array($saved['banners'] ?? null) ? $saved['banners'] : [];
        $defaults['button_text'] = is_string($saved['button_text'] ?? null) ? $saved['button_text'] : $defaults['button_text'];
        $defaults['button_icon'] = array_key_exists($saved['button_icon'] ?? '', self::buttonIcons()) ? $saved['button_icon'] : $defaults['button_icon'];
        $defaults['interaction_mode'] = in_array($saved['interaction_mode'] ?? '', ['hover', 'click'], true) ? $saved['interaction_mode'] : $defaults['interaction_mode'];
        return $defaults;
    }

    private static function attachmentId(mixed $id): int
    {
        $id = absint($id);
        return $id > 0 && get_post_type($id) === 'attachment' ? $id : 0;
    }

    private static function safeCssValue(string $value, string $default): string
    {
        $value = trim($value);
        if ($value === '' || str_contains($value, ';') || str_contains($value, '{') || str_contains($value, '}')) return $default;
        if (str_starts_with($default, 'linear-gradient')) return preg_match('/^linear-gradient\([#(),.%\s\-a-zA-Z0-9]+\)$/', $value) ? $value : $default;
        return preg_match('/^[#(),.%\s\-a-zA-Z0-9]+$/', $value) ? $value : $default;
    }
}
