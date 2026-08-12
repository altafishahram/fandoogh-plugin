<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Elementor\Modules\DynamicTags\Module;
use Fandoogh\Core\Managers\CustomerManager;
use Fandoogh\Elementor\BaseTag;

defined('ABSPATH') || exit;

final class CustomerProductCategories extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-customer-product-categories';
    }

    public function get_title(): string
    {
        return __('دسته‌های محصول مرتبط با مشتری', 'fandoogh');
    }

    public function get_categories(): array
    {
        return [Module::TEXT_CATEGORY];
    }

    public function get_value(array $options = []): mixed
    {
        $postId = (int) get_the_ID();

        if (! CustomerManager::isCustomer($postId)) {
            return '';
        }

        $names = [];

        foreach (CustomerManager::categories($postId) as $termId) {
            $term = get_term((int) $termId, 'product_cat');

            if ($term instanceof \WP_Term) {
                $names[] = $term->name;
            }
        }

        return implode('، ', $names);
    }
}
