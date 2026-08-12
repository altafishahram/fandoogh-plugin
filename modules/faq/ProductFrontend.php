<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

final class ProductFrontend
{
    public function __construct(
        private readonly bool $faqEnabled = true
    ) {
    }

    public function boot(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        if ($this->faqEnabled) {
            add_action('wp_footer', [$this, 'outputFaqSchema'], 31);
        }
    }

    public function enqueueAssets(): void
    {
        wp_enqueue_style(
            'fa-faq',
            FA_URL . 'assets/frontend/css/faq.css',
            [],
            FA_BUILD
        );
    }

    public function outputFaqSchema(): void
    {
        if (! function_exists('is_product') || ! is_product()) {
            return;
        }

        $productId = (int) get_queried_object_id();

        if ($productId <= 0 || ! ProductSchema::isFaqVisible($productId)) {
            return;
        }

        echo ProductSchema::renderFaq($productId); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
