<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Reviews\Renderer;

final class ReviewListTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-review-list';
    }

    public function get_title(): string
    {
        return __('فهرست نظرات', 'fandoogh');
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

        if (! $termId) {
            return '';
        }

        return Renderer::render($termId);
    }
}
