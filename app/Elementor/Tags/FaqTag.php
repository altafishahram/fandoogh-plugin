<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Faq\Faq;
use Fandoogh\Modules\Faq\Renderer;

/**
 * Elementor dynamic tag for the current product category FAQ.
 */
final class FaqTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-faq';
    }

    public function get_title(): string
    {
        return __('سؤالات متداول', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [
            Module::TEXT_CATEGORY,
        ];
    }

    public function get_value(array $options = []): mixed
    {
        $termId = $this->getCurrentTermId();

        if ($termId <= 0) {
            return '';
        }

        return Renderer::render($termId);
    }
}
