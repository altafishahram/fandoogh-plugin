<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\ContentTypes;

defined('ABSPATH') || exit;

/**
 * Review Proxy Post Type.
 *
 * Registers the hidden post type used
 * to store taxonomy reviews.
 *
 * @package Fandoogh\Modules\Reviews
 */
final class PostType
{
    /**
     * Boot.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'init',
            [$this, 'register']
        );
    }

    /**
     * Register proxy post type.
     *
     * @return void
     */
    public function register(): void
    {
        register_post_type(
            ContentTypes::REVIEW_PROXY,
            [
                'labels' => [
                    'name'          => __('واسط‌های نظرات', 'fandoogh'),
                    'singular_name' => __('واسط نظر', 'fandoogh'),
                ],

                'public'             => false,

                'publicly_queryable' => false,

                'show_ui'            => false,

                'show_in_menu'       => false,

                'show_in_admin_bar'  => false,

                'show_in_nav_menus'  => false,

                'exclude_from_search'=> true,

                'has_archive'        => false,

                'rewrite'            => false,

                'query_var'          => false,

                'supports' => [
                    'title',
                ],
            ]
        );
    }
}
