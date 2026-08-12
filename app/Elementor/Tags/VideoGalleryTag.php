<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Video\Video;

final class VideoGalleryTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-video-gallery';
    }

    public function get_title(): string
    {
        return __('گالری ویدئو', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [
            Module::GALLERY_CATEGORY,
        ];
    }

    public function get_value(array $options = []): mixed
    {
        $termId = $this->getCurrentTermId();

        if (! $termId) {
            return [];
        }

        $images = [];

        foreach ((array) (Video::get($termId)['gallery'] ?? []) as $id) {
            $url = wp_get_attachment_image_url((int) $id, 'full');
            if ($url) $images[] = ['id' => (int) $id, 'url' => $url];
        }

        return $images;
    }
}
