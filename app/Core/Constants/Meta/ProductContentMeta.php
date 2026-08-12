<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants\Meta;

defined('ABSPATH') || exit;

/**
 * Product content meta keys owned by the FAQ module.
 */
final class ProductContentMeta
{
    public const FAQ = 'fa_product_faq';
    public const REASON_QUESTION = 'fa_product_reason_question';
    public const REASON_ANSWER = 'fa_product_reason_answer';

    private function __construct()
    {
    }
}
