<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

use Fandoogh\Customers\Taxonomy;

defined('ABSPATH') || exit;

final class CustomerCategories extends CustomerFieldTag
{
    public function get_name(): string { return 'fa-customer-categories'; }
    public function get_title(): string { return __('دسته‌بندی مشتری', 'fandoogh'); }
    public function get_value(array $options = []): mixed
    {
        $terms = get_the_terms($this->customerId(), Taxonomy::NAME);
        return is_array($terms)
            ? implode('، ', wp_list_pluck($terms, 'name'))
            : '';
    }
}
