<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class CustomerAddress extends CustomerFieldTag
{
    public function get_name(): string { return 'fa-customer-address'; }
    public function get_title(): string { return __('آدرس مشتری', 'fandoogh'); }
    public function get_value(array $options = []): mixed
    {
        $postId = $this->customerId();
        return $postId > 0 ? CustomerManager::address($postId) : '';
    }
}
