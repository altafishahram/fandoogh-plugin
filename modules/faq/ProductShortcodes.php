<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Fandoogh\Core\Constants\Shortcodes;

defined('ABSPATH') || exit;

final class ProductShortcodes
{
    public function __construct(
        private readonly bool $faqEnabled = true,
        private readonly bool $reasonEnabled = true
    ) {
    }

    public function boot(): void
    {
        if ($this->faqEnabled) {
            add_shortcode(Shortcodes::PRODUCT_FAQ, [$this, 'faq']);
        }

        if ($this->reasonEnabled) {
            add_shortcode(Shortcodes::PRODUCT_REASON, [$this, 'reason']);
        }
    }

    public function faq(array $attributes = []): string
    {
        return ProductRenderer::faq($this->productId($attributes));
    }

    public function reason(array $attributes = []): string
    {
        return ProductRenderer::reason($this->productId($attributes));
    }

    private function productId(array $attributes): int
    {
        $attributes = shortcode_atts(['id' => 0], $attributes);
        $productId = absint($attributes['id']) ?: (int) get_the_ID();

        return ProductService::isProduct($productId) ? $productId : 0;
    }
}
