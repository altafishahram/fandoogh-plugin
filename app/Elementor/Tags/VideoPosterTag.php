<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Video\Video;

final class VideoPosterTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-video-poster';
    }

    public function get_title(): string
    {
        return __('پوستر ویدئو', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [
            Module::IMAGE_CATEGORY,
        ];
    }

    public function get_value(array $options = []): mixed
    {
        $termId = $this->getCurrentTermId();

        if (! $termId) {
            return '';
        }

        $id = (int) (Video::get($termId)['poster'] ?? 0);
        $url = $id > 0 ? wp_get_attachment_image_url($id, 'full') : false;

        return $url ? ['id' => $id, 'url' => $url] : [];
    }
}
