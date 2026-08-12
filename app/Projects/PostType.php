<?php

declare(strict_types=1);

namespace Fandoogh\Projects;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\ProjectMeta;
use Fandoogh\Core\Managers\ProjectManager;

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
        register_post_type(ContentTypes::PROJECT, [
            'labels' => [
                'name' => __('پروژه‌ها', 'fandoogh'),
                'singular_name' => __('پروژه', 'fandoogh'),
                'menu_name' => __('پروژه‌ها', 'fandoogh'),
                'name_admin_bar' => __('پروژه', 'fandoogh'),
                'add_new' => __('افزودن پروژه', 'fandoogh'),
                'add_new_item' => __('افزودن پروژه جدید', 'fandoogh'),
                'new_item' => __('پروژه جدید', 'fandoogh'),
                'edit_item' => __('ویرایش پروژه', 'fandoogh'),
                'view_item' => __('مشاهده پروژه', 'fandoogh'),
                'all_items' => __('همه پروژه‌ها', 'fandoogh'),
                'search_items' => __('جست‌وجوی پروژه‌ها', 'fandoogh'),
                'not_found' => __('پروژه‌ای یافت نشد.', 'fandoogh'),
                'not_found_in_trash' => __('پروژه‌ای در زباله‌دان نیست.', 'fandoogh'),
            ],
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'show_in_rest' => true,
            'menu_position' => 26,
            'menu_icon' => 'dashicons-building',
            'supports' => ['title', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => ['slug' => 'projects', 'with_front' => false],
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false,
        ]);
    }

    public function registerMeta(): void
    {
        register_post_meta(ContentTypes::PROJECT, ProjectMeta::DATA, [
            'type' => 'object',
            'single' => true,
            'show_in_rest' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'contractor' => ['type' => 'string'],
                        'excerpt' => ['type' => 'string'],
                        'address' => ['type' => 'string'],
                        'video' => ['type' => 'string', 'format' => 'uri'],
                        'gallery' => ['type' => 'array', 'items' => ['type' => 'integer']],
                        'categories' => ['type' => 'array', 'items' => ['type' => 'integer']],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'sanitize_callback' => [ProjectManager::class, 'sanitize'],
            'auth_callback' => static fn (bool $allowed, string $metaKey, int $postId): bool => current_user_can('edit_post', $postId),
        ]);
    }
}
