<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Repository
{
    public const OPTION_KEY = 'fa_mega_menu_settings';

    public static function settings(): array
    {
        $settings = get_option(self::OPTION_KEY, []);
        return is_array($settings) ? $settings : [];
    }

    public static function save(array $settings): void
    {
        update_option(self::OPTION_KEY, $settings);
    }

    /** @return array<int, \WP_Term> */
    public static function parentCategories(bool $hideEmpty = true): array
    {
        if (! taxonomy_exists('product_cat')) {
            return [];
        }

        $terms = get_terms([
            'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => $hideEmpty,
            'orderby' => 'menu_order', 'order' => 'ASC',
        ]);
        return is_wp_error($terms) ? [] : $terms;
    }

    /** @return array<int, \WP_Term> */
    public static function children(int $termId): array
    {
        $terms = get_terms([
            'taxonomy' => 'product_cat', 'parent' => $termId, 'hide_empty' => true,
            'orderby' => 'menu_order', 'order' => 'ASC',
        ]);
        return is_wp_error($terms) ? [] : $terms;
    }

    public static function imageUrl(int $termId): string
    {
        $id = absint(get_term_meta($termId, 'thumbnail_id', true));
        return $id ? (string) wp_get_attachment_image_url($id, 'thumbnail') : '';
    }
}
