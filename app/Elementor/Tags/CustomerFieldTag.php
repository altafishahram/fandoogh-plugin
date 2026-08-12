<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Core\Managers\CustomerManager;
use Fandoogh\Elementor\BaseTag;

defined('ABSPATH') || exit;

abstract class CustomerFieldTag extends BaseTag
{
    public function get_categories(): array
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function customerId(): int
    {
        $postId = (int) get_the_ID();
        return CustomerManager::isCustomer($postId) ? $postId : 0;
    }
}
