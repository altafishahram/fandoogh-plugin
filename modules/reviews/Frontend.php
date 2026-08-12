<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\Assets;
use Fandoogh\Core\Constants\Shortcodes;

defined('ABSPATH') || exit;

/**
 * Reviews Frontend.
 *
 * Handles frontend assets, hooks,
 * shortcode rendering and templates.
 *
 * @package Fandoogh\Modules\Reviews
 */
final class Frontend
{
    /**
     * Boot frontend.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueueAssets']
        );

        add_shortcode(
            Shortcodes::REVIEWS,
            [$this, 'renderShortcode']
        );
    }

    /**
     * Enqueue assets.
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        if (! function_exists('is_product_category') || ! is_product_category()) {
            return;
        }

        wp_enqueue_style(
            Assets::REVIEWS,
            FA_URL . 'assets/admin/css/reviews.css',
            [],
            FA_VERSION
        );

        wp_enqueue_script(
            Assets::REVIEWS,
            FA_URL . 'assets/admin/js/reviews.js',
            [],
            FA_VERSION,
            true
        );

        wp_localize_script(
            Assets::REVIEWS,
            'faReviews',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('fa_reviews'),
            ]
        );
    }

    /**
     * Render shortcode.
     *
     * @return string
     */
    public function renderShortcode(): string
    {
        if (! function_exists('is_product_category') || ! is_product_category()) {
            return '';
        }

        $term = get_queried_object();

        if (! $term instanceof \WP_Term) {
            return '';
        }

        return Renderer::render((int) $term->term_id);
    }
    /**
     * Render reviews.
     *
     * @param int   $termId
     * @param array $args
     *
     * @return void
     */
    public static function render(
        int $termId,
        array $args = []
    ): void {

        echo Renderer::render($termId, $args); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
