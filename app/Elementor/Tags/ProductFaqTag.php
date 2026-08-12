<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Faq\ProductRenderer;

defined('ABSPATH') || exit;

final class ProductFaqTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-product-faq';
    }

    public function get_title(): string
    {
        return 'سؤالات متداول محصول';
    }

    public function get_value(array $options = []): mixed
    {
        return ProductRenderer::faq($this->currentPostId());
    }
}
