<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class MobileShortcode
{
    public function boot(): void
    {
        add_shortcode('fa_mobile_category_menu', [$this, 'render']);
    }

    public function render(array|string $attributes = []): string
    {
        if (is_admin() && ! wp_doing_ajax()) {
            return '';
        }

        $atts = shortcode_atts(['class' => '', 'home_label' => __('خانه', 'fandoogh')], (array) $attributes, 'fa_mobile_category_menu');
        return MobileRenderer::render([
            'class' => sanitize_html_class((string) $atts['class']),
            'home_label' => sanitize_text_field((string) $atts['home_label']),
        ]);
    }
}
