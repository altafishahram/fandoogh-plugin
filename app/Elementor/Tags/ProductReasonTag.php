<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Faq\ProductRenderer;

defined('ABSPATH') || exit;

final class ProductReasonTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-product-reason';
    }

    public function get_title(): string
    {
        return 'پاسخ تک‌سؤالی محصول';
    }

    public function get_value(array $options = []): mixed
    {
        return ProductRenderer::reason($this->currentPostId());
    }
}
