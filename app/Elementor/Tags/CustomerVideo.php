<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class CustomerVideo extends CustomerFieldTag
{
    public function get_categories(): array { return [\Elementor\Modules\DynamicTags\Module::URL_CATEGORY]; }
    public function get_name(): string { return 'fa-customer-video'; }
    public function get_title(): string { return __('نشانی ویدئوی مشتری', 'fandoogh'); }
    public function get_value(array $options = []): mixed
    {
        $postId = $this->customerId();
        return $postId > 0 ? CustomerManager::video($postId) : '';
    }
}
