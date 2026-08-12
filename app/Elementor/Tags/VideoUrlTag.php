<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Video\Video;

final class VideoUrlTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-video-url';
    }

    public function get_title(): string
    {
        return __('نشانی ویدئو', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [
            Module::URL_CATEGORY,
        ];
    }

    public function get_value(array $options = []): mixed
    {
        $termId = $this->getCurrentTermId();

        if (! $termId) {
            return '';
        }

        return Video::get($termId)['url'] ?? '';
    }
}
