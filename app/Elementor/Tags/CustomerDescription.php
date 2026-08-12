<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class CustomerDescription extends CustomerFieldTag
{
    public function get_name(): string { return 'fa-customer-description'; }
    public function get_title(): string { return __('توضیحات مشتری', 'fandoogh'); }
    public function get_value(array $options = []): mixed
    {
        $postId = $this->customerId();
        return $postId > 0 ? CustomerManager::excerpt($postId) : '';
    }
}
