<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\CustomerMeta;
use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class PostType
{
    public function register(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerMeta']);
    }

    public function registerPostType(): void
    {
        register_post_type(ContentTypes::CUSTOMER, [
            'labels' => [
                'name' => __('مشتریان', 'fandoogh'),
                'singular_name' => __('مشتری', 'fandoogh'),
                'menu_name' => __('مشتریان', 'fandoogh'),
                'name_admin_bar' => __('مشتری', 'fandoogh'),
                'add_new' => __('افزودن مشتری', 'fandoogh'),
                'add_new_item' => __('افزودن مشتری جدید', 'fandoogh'),
                'new_item' => __('مشتری جدید', 'fandoogh'),
                'edit_item' => __('ویرایش مشتری', 'fandoogh'),
                'view_item' => __('مشاهده مشتری', 'fandoogh'),
                'all_items' => __('همه مشتریان', 'fandoogh'),
                'search_items' => __('جست‌وجوی مشتریان', 'fandoogh'),
                'not_found' => __('مشتری پیدا نشد.', 'fandoogh'),
                'not_found_in_trash' => __('مشتری در زباله‌دان پیدا نشد.', 'fandoogh'),
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'customers'],
            'menu_position' => 30,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'thumbnail'],
            'hierarchical' => false,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'can_export' => true,
            'delete_with_user' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    public function registerMeta(): void
    {
        register_post_meta(ContentTypes::CUSTOMER, CustomerMeta::DATA, [
            'type' => 'object',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'excerpt' => ['type' => 'string'],
                        'address' => ['type' => 'string'],
                        'video' => ['type' => 'string', 'format' => 'uri'],
                        'gallery' => [
                            'type' => 'array',
                            'items' => ['type' => 'integer'],
                        ],
                        'categories' => [
                            'type' => 'array',
                            'items' => ['type' => 'integer'],
                        ],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'sanitize_callback' => [CustomerManager::class, 'sanitize'],
            'auth_callback' => static fn (bool $allowed, string $metaKey, int $postId): bool => current_user_can('edit_post', $postId),
        ]);
    }
}
