<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Options;
use Fandoogh\Core\Constants\Taxonomies;
defined('ABSPATH') || exit;
final class Taxonomy
{
    public const NAME = Taxonomies::PROJECT_CATEGORY;
    public function register(): void { add_action('init', [$this, 'registerTaxonomy']); }
    public function registerTaxonomy(): void
    {
        register_taxonomy(self::NAME, [ContentTypes::PROJECT], [
            'labels' => [
                'name' => __('دسته‌بندی پروژه‌ها', 'fandoogh'),
                'singular_name' => __('دسته پروژه', 'fandoogh'),
                'menu_name' => __('دسته‌بندی‌ها', 'fandoogh'),
                'all_items' => __('همه دسته‌ها', 'fandoogh'),
                'edit_item' => __('ویرایش دسته پروژه', 'fandoogh'),
                'add_new_item' => __('افزودن دسته پروژه', 'fandoogh'),
                'search_items' => __('جست‌وجوی دسته‌های پروژه', 'fandoogh'),
            ],
            'public' => true, 'hierarchical' => true, 'show_ui' => true,
            'show_admin_column' => true, 'show_in_rest' => true,
            'rewrite' => ['slug' => 'project-category'],
        ]);
        if (get_option(Options::PROJECT_REWRITE_VERSION) !== '1') {
            flush_rewrite_rules(false);
            update_option(Options::PROJECT_REWRITE_VERSION, '1', false);
        }
    }
}
