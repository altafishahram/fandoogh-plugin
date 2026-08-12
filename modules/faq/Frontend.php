<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

/**
 * Frontend
 *
 * Handles frontend functionality for the FAQ module.
 *
 * @package Fandoogh\Modules\Faq
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

        add_action(
            'wp_footer',
            [$this, 'outputSchema'],
            30
        );
    }

    /**
     * Enqueue the accessible accordion styles.
     */
    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'fa-faq',
            FA_URL . 'assets/frontend/css/faq.css',
            [],
            FA_VERSION
        );
    }

    /**
     * Output FAQPage structured data for the current product category.
     */
    public function outputSchema(): void
    {
        $term = get_queried_object();

        if (
            ! function_exists('is_product_category')
            || ! is_product_category()
            || ! $term instanceof \WP_Term
            || $term->taxonomy !== 'product_cat'
        ) {
            return;
        }

        if (! Schema::isVisible($term->term_id)) {
            return;
        }

        echo Schema::render($term->term_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Get FAQ items.
     *
     * @param int $termId
     *
     * @return array
     */
    public static function get(int $termId): array
    {
        return Faq::get($termId);
    }

    /**
     * Check if FAQ exists.
     *
     * @param int $termId
     *
     * @return bool
     */
    public static function hasFaq(int $termId): bool
    {
        return Faq::exists($termId);
    }

    /**
     * Get all FAQ data.
     *
     * @param int $termId
     *
     * @return array
     */
    public static function data(int $termId): array
    {
        return Faq::get($termId);
    }
}
