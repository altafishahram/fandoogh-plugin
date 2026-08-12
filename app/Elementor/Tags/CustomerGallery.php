<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class CustomerGallery extends CustomerFieldTag
{
    public function get_name(): string { return 'fa-customer-gallery'; }
    public function get_title(): string { return __('گالری مشتری', 'fandoogh'); }
    public function get_categories(): array { return [\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY]; }
    public function get_value(array $options = []): mixed
    {
        $postId = $this->customerId();
        $urls = [];

        foreach (CustomerManager::gallery($postId) as $imageId) {
            $url = wp_get_attachment_image_url((int) $imageId, 'full');
            if ($url) {
                $urls[] = ['id' => (int) $imageId, 'url' => $url];
            }
        }

        return $urls;
    }
}
