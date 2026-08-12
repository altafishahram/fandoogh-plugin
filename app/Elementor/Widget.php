<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

use Elementor\Widget_Base;

defined('ABSPATH') || exit;

/**
 * Base Elementor Widget.
 *
 * All Fandoogh widgets should extend this class.
 *
 * @package Fandoogh\Elementor
 */
abstract class Widget extends Widget_Base
{
    /**
     * Widget Category.
     *
     * @return array
     */
    public function get_categories(): array
    {
        return [
            'fandoogh',
        ];
    }

    /**
     * Widget Keywords.
     *
     * @return array
     */
    public function get_keywords(): array
    {
        return [
            'fandoogh',
            'woocommerce',
            'product',
            'category',
        ];
    }

    /**
     * Register Controls.
     *
     * @return void
     */
    protected function register_controls(): void
    {
        $this->registerWidgetControls();
    }

    /**
     * Render Widget.
     *
     * @return void
     */
    protected function render(): void
    {
        $this->renderWidget();
    }

    /**
     * Register widget controls.
     *
     * @return void
     */
    abstract protected function registerWidgetControls(): void;

    /**
     * Render widget.
     *
     * @return void
     */
    abstract protected function renderWidget(): void;

    /**
     * Check current page.
     *
     * @return bool
     */
    protected function isProductCategory(): bool
    {
        if (function_exists('is_product_category') && is_product_category()) {
            return true;
        }

        $term = get_queried_object();
        return $term instanceof \WP_Term && $term->taxonomy === 'product_cat';
    }

    /**
     * Get current queried term.
     *
     * @return \WP_Term|null
     */
    protected function currentTerm(): ?\WP_Term
    {
        $term = get_queried_object();

        return $term instanceof \WP_Term
            ? $term
            : null;
    }

    /**
     * Get current term ID.
     *
     * @return int
     */
    protected function currentTermId(): int
    {
        return $this->currentTerm()?->term_id ?? 0;
    }

    /**
     * Check queried term exists.
     *
     * @return bool
     */
    protected function hasTerm(): bool
    {
        return $this->currentTerm() !== null;
    }

    /**
     * Render a view.
     *
     * @param string $view
     * @param array  $data
     *
     * @return void
     */
    protected function renderView(
        string $view,
        array $data = []
    ): void {

        if ($data !== []) {
            extract(
                $data,
                EXTR_SKIP
            );
        }

        require $view;
    }

    /**
     * Render empty message.
     *
     * @param string $message
     *
     * @return void
     */
    protected function emptyMessage(
        string $message
    ): void {

        printf(
            '<div class="fa-empty">%s</div>',
            esc_html($message)
        );
    }

    /**
     * Output HTML safely.
     *
     * @param string $content
     *
     * @return void
     */
    protected function output(
        string $content
    ): void {

        echo wp_kses_post(
            $content
        );
    }

    /**
     * Get widget setting.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function setting(
        string $key,
        mixed $default = null
    ): mixed {

        $value = $this->get_settings_for_display(
            $key
        );

        return $value !== null && $value !== ''
            ? $value
            : $default;
    }
}
