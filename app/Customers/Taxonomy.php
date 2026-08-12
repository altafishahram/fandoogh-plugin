<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Options;
use Fandoogh\Core\Constants\Taxonomies;

defined('ABSPATH') || exit;

final class Taxonomy
{
    public const NAME = Taxonomies::CUSTOMER_CATEGORY;

    public function register(): void
    {
        add_action('init', [$this, 'registerTaxonomy']);
    }

    public function registerTaxonomy(): void
    {
        register_taxonomy(self::NAME, [ContentTypes::CUSTOMER], [
            'labels' => [
                'name' => __('دسته‌بندی مشتریان', 'fandoogh'),
                'singular_name' => __('دسته مشتری', 'fandoogh'),
                'menu_name' => __('دسته‌بندی‌ها', 'fandoogh'),
                'all_items' => __('همه دسته‌ها', 'fandoogh'),
                'edit_item' => __('ویرایش دسته مشتری', 'fandoogh'),
                'view_item' => __('مشاهده دسته مشتری', 'fandoogh'),
                'update_item' => __('به‌روزرسانی دسته مشتری', 'fandoogh'),
                'add_new_item' => __('افزودن دسته مشتری', 'fandoogh'),
                'new_item_name' => __('نام دسته مشتری جدید', 'fandoogh'),
                'search_items' => __('جست‌وجوی دسته‌های مشتری', 'fandoogh'),
                'not_found' => __('دسته‌ای پیدا نشد.', 'fandoogh'),
            ],
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'customer-category'],
        ]);

        if (get_option(Options::CUSTOMER_REWRITE_VERSION) !== '1') {
            flush_rewrite_rules(false);
            update_option(Options::CUSTOMER_REWRITE_VERSION, '1', false);
        }
    }
}
