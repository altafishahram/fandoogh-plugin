<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Reviews\Reviews;

final class ReviewCountTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-review-count';
    }

    public function get_title(): string
    {
        return __('تعداد نظرات', 'fandoogh');
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
            return 0;
        }

        return Reviews::count($termId);
    }
}
