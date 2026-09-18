<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Renderer
{
    public static function render(array $args = []): string
    {
        if (! taxonomy_exists('product_cat')) return '';
        $categories = Repository::parentCategories();
        if ($categories === []) return '';
        $settings = Service::settings();
        $button = ($args['button_text'] ?? '') !== '' ? $args['button_text'] : $settings['button_text'];
        $icon = $args['button_icon'] ?? $settings['button_icon'];
        $icon = array_key_exists($icon, Service::buttonIcons()) ? $icon : $settings['button_icon'];
        $interaction = in_array($args['interaction_mode'] ?? '', ['hover', 'click'], true) ? $args['interaction_mode'] : $settings['interaction_mode'];
        $instance = wp_unique_id('fa-mega-');
        $class = trim('fa-mega-wrapper ' . ($args['class'] ?? ''));
        $html = '<div class="' . esc_attr($class) . '" data-fa-mega-menu data-interaction-mode="' . esc_attr($interaction) . '" style="' . Service::cssVariables($settings, (array) ($args['styles'] ?? [])) . '">';
        $html .= '<button type="button" class="fa-mega-trigger" aria-expanded="false" aria-controls="' . esc_attr($instance . '-dropdown') . '"><span class="dashicons ' . esc_attr($icon) . '" aria-hidden="true"></span><span>' . esc_html((string) $button) . '</span><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>';
        $html .= '<div id="' . esc_attr($instance . '-dropdown') . '" class="fa-mega-dropdown" aria-hidden="true"><button type="button" class="fa-mega-close" aria-label="' . esc_attr__('بستن مگا منو', 'fandoogh') . '"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button><div class="fa-mega-sidebar" role="tablist" aria-label="' . esc_attr__('دسته‌بندی محصولات', 'fandoogh') . '">';
        foreach ($categories as $index => $category) $html .= self::categoryTab($category, $index === 0, $instance);
        $html .= '</div><div class="fa-mega-content">';
        foreach ($categories as $index => $category) $html .= self::panel($category, $index === 0, $instance, $settings);
        return $html . '</div></div></div>';
    }

    private static function categoryTab(\WP_Term $term, bool $active, string $instance): string
    {
        $icon = Repository::imageUrl($term->term_id);
        $image = $icon !== '' ? '<img src="' . esc_url($icon) . '" alt="" class="fa-mega-cat-icon">' : '<span class="dashicons dashicons-category" aria-hidden="true"></span>';
        return '<button type="button" class="fa-mega-cat' . ($active ? ' is-active' : '') . '" role="tab" aria-selected="' . ($active ? 'true' : 'false') . '" aria-controls="' . esc_attr($instance . '-panel-' . $term->term_id) . '" data-panel="' . esc_attr($instance . '-panel-' . $term->term_id) . '">' . $image . '<span>' . esc_html($term->name) . '</span></button>';
    }

    private static function panel(\WP_Term $term, bool $active, string $instance, array $settings): string
    {
        $link = get_term_link($term);
        $url = is_wp_error($link) ? '' : $link;
        $html = '<section id="' . esc_attr($instance . '-panel-' . $term->term_id) . '" class="fa-mega-panel' . ($active ? ' is-active' : '') . '" role="tabpanel"' . ($active ? '' : ' hidden') . '>';
        $html .= '<header class="fa-mega-panel-header"><h3>' . esc_html($term->name) . '</h3>' . ($url !== '' ? '<a href="' . esc_url($url) . '">' . esc_html__('مشاهده همه', 'fandoogh') . '<span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span></a>' : '') . '</header>';
        $children = Repository::children($term->term_id);
        if ($children === []) $html .= '<p class="fa-mega-empty">' . esc_html__('این دسته‌بندی زیر‌دسته‌ای ندارد.', 'fandoogh') . '</p>';
        else { $html .= '<div class="fa-mega-subcat-grid">'; foreach ($children as $child) $html .= self::childCard($child); $html .= '</div>'; }
        return $html . self::banner($term, $url, $settings) . '</section>';
    }

    private static function childCard(\WP_Term $term): string
    {
        $link = get_term_link($term); if (is_wp_error($link)) return '';
        $image = Repository::imageUrl($term->term_id);
        $media = $image !== '' ? '<img src="' . esc_url($image) . '" alt="" loading="lazy">' : '<span class="dashicons dashicons-tag" aria-hidden="true"></span>';
        return '<a href="' . esc_url($link) . '" class="fa-mega-subcat-item"><span class="fa-mega-subcat-icon">' . $media . '</span><span class="fa-mega-subcat-text"><strong>' . esc_html($term->name) . '</strong><small>' . esc_html(sprintf(_n('%s محصول', '%s محصول', $term->count, 'fandoogh'), number_format_i18n($term->count))) . '</small></span></a>';
    }

    private static function banner(\WP_Term $term, string $fallbackUrl, array $settings): string
    {
        $banner = $settings['banners'][$term->term_id] ?? [];
        if (($banner['type'] ?? '') === 'image' && ! empty($banner['image_id'])) {
            $image = wp_get_attachment_image((int) $banner['image_id'], 'medium_large', false, ['loading' => 'lazy']);
            if ($image) return '<div class="fa-mega-banner fa-mega-banner-image">' . $image . '</div>';
        }
        if (($banner['type'] ?? '') === 'html' && ! empty($banner['html'])) return '<div class="fa-mega-banner fa-mega-banner-html">' . wp_kses_post($banner['html']) . '</div>';
        $title = $banner['title'] ?? sprintf(__('پیشنهاد ویژه %s', 'fandoogh'), $term->name);
        $text = $banner['text'] ?? __('محصولات منتخب این دسته را ببینید.', 'fandoogh');
        $button = $banner['button_text'] ?? __('مشاهده پیشنهادات', 'fandoogh');
        $url = $banner['button_url'] ?? $fallbackUrl;
        $html = '<div class="fa-mega-banner"><div><h4>' . esc_html((string) $title) . '</h4><p>' . esc_html((string) $text) . '</p></div>';
        return $html . ($url !== '' && $button !== '' ? '<a class="fa-mega-banner-btn" href="' . esc_url((string) $url) . '">' . esc_html((string) $button) . '</a>' : '') . '</div>';
    }
}
