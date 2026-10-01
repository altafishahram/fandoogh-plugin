<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class MobileRenderer
{
    public static function render(array $args = []): string
    {
        if (! taxonomy_exists('product_cat')) {
            return '';
        }

        $settings = Service::settings();
        $categoryIds = is_array($args['category_ids'] ?? null) && $args['category_ids'] !== []
            ? array_values(array_filter(array_map('absint', $args['category_ids'])))
            : (($settings['category_display_mode'] ?? 'all') === 'selected' ? (array) ($settings['visible_category_ids'] ?? []) : []);
        $terms = Repository::parentCategories(true, $categoryIds);
        if ($terms === []) {
            return '';
        }

        Frontend::enqueueMobile();
        $instance = wp_unique_id('fa-mobile-nav-');
        $home = $args['home_label'] !== '' ? $args['home_label'] : __('خانه', 'fandoogh');
        $class = trim('fa-mobile-nav ' . ($args['class'] ?? ''));
        $html = '<nav id="' . esc_attr($instance) . '" class="' . esc_attr($class) . '" data-fa-mobile-nav data-home-label="' . esc_attr($home) . '" aria-label="' . esc_attr__('دسته‌بندی محصولات', 'fandoogh') . '" style="' . Service::cssVariables($settings, (array) ($args['styles'] ?? [])) . '">';
        $html .= '<header class="fa-mobile-nav-header"><button class="fa-mobile-nav-back" type="button" hidden><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span><span>' . esc_html__('بازگشت', 'fandoogh') . '</span></button><p class="fa-mobile-nav-crumb">' . esc_html($home) . '</p></header>';
        $html .= self::level('root', $terms);
        foreach ($terms as $term) {
            $html .= self::descendants($term);
        }
        return $html . '</nav>';
    }

    private static function descendants(\WP_Term $term): string
    {
        $children = Repository::children($term->term_id);
        if ($children === []) {
            return '';
        }

        $html = self::level((string) $term->term_id, $children, true);
        foreach ($children as $child) {
            $html .= self::descendants($child);
        }
        return $html;
    }

    /** @param array<int, \WP_Term> $terms */
    private static function level(string $id, array $terms, bool $hidden = false): string
    {
        $html = '<ul class="fa-mobile-nav-level" data-level="' . esc_attr($id) . '"' . ($hidden ? ' hidden' : '') . '>';
        foreach ($terms as $term) {
            $children = Repository::children($term->term_id);
            $link = get_term_link($term);
            $content = self::image($term) . '<span class="fa-mobile-nav-label">' . esc_html($term->name) . '</span>';
            if ($children !== []) {
                $html .= '<li><button type="button" class="fa-mobile-nav-item" data-target="' . esc_attr((string) $term->term_id) . '" data-label="' . esc_attr($term->name) . '"><span class="fa-mobile-nav-item-content">' . $content . '</span><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button></li>';
            } elseif (! is_wp_error($link)) {
                $html .= '<li><a class="fa-mobile-nav-item" href="' . esc_url($link) . '"><span class="fa-mobile-nav-item-content">' . $content . '</span><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></a></li>';
            }
        }
        return $html . '</ul>';
    }

    private static function image(\WP_Term $term): string
    {
        $imageId = absint(get_term_meta($term->term_id, 'thumbnail_id', true));
        if ($imageId === 0) {
            return '';
        }

        $image = wp_get_attachment_image($imageId, 'thumbnail', false, [
            'class' => 'fa-mobile-nav-image',
            'alt' => $term->name,
            'loading' => 'lazy',
        ]);
        return $image ? '<span class="fa-mobile-nav-image-wrap">' . $image . '</span>' : '';
    }
}
