<?php

declare(strict_types=1);

namespace Fandoogh\Elementor\Tags;

defined('ABSPATH') || exit;

use Fandoogh\Elementor\BaseTag;
use Fandoogh\Modules\Description\Description;

class DescriptionTag extends BaseTag
{
    public function get_name(): string
    {
        return 'fa-description';
    }

    public function get_title(): string
    {
        return esc_html__( 'توضیحات', 'fandoogh' );
    }

    public function get_categories(): array
    {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    public function get_value( array $options = [] ): mixed
    {
        $termId = $this->getCurrentTermId();

        if ( ! $termId ) {
            return '';
        }

        return Description::get( $termId );
    }
}
