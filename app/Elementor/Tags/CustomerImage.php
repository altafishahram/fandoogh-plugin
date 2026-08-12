<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

final class CustomerImage extends CustomerFieldTag
{
    public function get_name(): string { return 'fa-customer-image'; }
    public function get_title(): string { return __('تصویر شاخص مشتری', 'fandoogh'); }
    public function get_categories(): array { return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY]; }
    public function get_value(array $options = []): mixed
    {
        $id = get_post_thumbnail_id($this->customerId());
        $url = $id ? wp_get_attachment_image_url($id, 'full') : false;
        return $url ? ['id' => $id, 'url' => $url] : [];
    }
}
