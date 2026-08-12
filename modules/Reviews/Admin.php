<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

defined('ABSPATH') || exit;

/**
 * Reviews Admin.
 *
 * Handles admin functionality for the
 * Reviews module.
 *
 * @package Fandoogh\Modules\Reviews
 */
final class Admin
{
    /**
     * Boot admin.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'admin_enqueue_scripts',
            [$this, 'enqueueAssets']
        );

        add_filter(
            'manage_edit-comments_columns',
            [$this, 'columns']
        );

        add_action(
            'manage_comments_custom_column',
            [$this, 'columnContent'],
            10,
            2
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'fa-reviews-admin',
            FA_URL . 'assets/admin/css/reviews.css',
            [],
            FA_VERSION
        );
    }

    /**
     * Register custom columns.
     *
     * @param array $columns
     *
     * @return array
     */
    public function columns(array $columns): array
    {
        $columns['fa_rating'] = __(
            'امتیاز',
            'fandoogh'
        );

        return $columns;
    }

    /**
     * Render custom column.
     *
     * @param string $column
     * @param int    $commentId
     *
     * @return void
     */
    public function columnContent(
        string $column,
        int $commentId
    ): void {

        if ($column !== 'fa_rating') {
            return;
        }

        $rating = (int) get_comment_meta(
            $commentId,
            'fa_rating',
            true
        );

        echo esc_html(
            (string) $rating
        );
    }
}
