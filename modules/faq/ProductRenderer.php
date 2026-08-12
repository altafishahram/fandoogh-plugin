<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

final class ProductRenderer
{
    public static function faq(int $productId, bool $firstOpen = true): string
    {
        if (! ProductService::isProduct($productId)) {
            return '';
        }

        $html = '';

        foreach (ProductService::faq($productId) as $index => $item) {
            $html .= '<details class="fa-faq-item"' . ($firstOpen && $index === 0 ? ' open' : '') . '>';
            $html .= '<summary class="fa-faq-question">' . esc_html((string) $item['question']);
            $html .= '<span class="fa-faq-toggle" aria-hidden="true"></span></summary>';
            $html .= '<div class="fa-faq-answer">' . wpautop((string) $item['answer']) . '</div></details>';
        }

        if ($html === '') {
            return '';
        }

        ProductSchema::markFaqVisible($productId);

        return '<div class="fa-faq fa-product-faq">' . $html . '</div>';
    }

    public static function reason(int $productId): string
    {
        if (! ProductService::isProduct($productId)) {
            return '';
        }

        $reason = ProductService::reason($productId);

        if ($reason['question'] === '' || $reason['answer'] === '') {
            return '';
        }

        ProductSchema::markReasonVisible($productId);
        $headingId = 'fa-product-reason-title-' . $productId;

        return '<section class="fa-product-reason" aria-labelledby="' . esc_attr($headingId) . '">'
            . '<h2 class="fa-product-reason__title" id="' . esc_attr($headingId) . '">'
            . esc_html($reason['question'])
            . '</h2><div class="fa-product-reason__answer">'
            . wpautop($reason['answer'])
            . '</div></section>';
    }
}
