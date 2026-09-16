<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Shortcode
{
    public function boot(): void
    {
        add_shortcode('fa_mega_menu', [$this, 'render']);
    }

    public function render(array|string $attributes = []): string
    {
        if (is_admin() && ! wp_doing_ajax()) return '';
        $atts = shortcode_atts(['button_text' => '', 'class' => ''], (array) $attributes, 'fa_mega_menu');
        return Renderer::render([
            'button_text' => sanitize_text_field((string) $atts['button_text']),
            'class' => sanitize_html_class((string) $atts['class']),
        ]);
    }
}
