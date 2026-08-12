<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Core\Managers\CustomerManager;
use Fandoogh\Elementor\BaseTag;

defined('ABSPATH') || exit;

final class CustomerName extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-customer-name';
    }

    public function get_title(): string
    {
        return __('نام مشتری', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [Module::TEXT_CATEGORY];
    }

    public function get_value(array $options = []): mixed
    {
        $postId = (int) get_the_ID();

        return CustomerManager::isCustomer($postId)
            ? CustomerManager::title($postId)
            : '';
    }
}
