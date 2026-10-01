<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

use Elementor\Controls_Manager;
use Fandoogh\Elementor\Widget;

defined('ABSPATH') || exit;

final class MobileElementorWidget extends Widget
{
    public function get_name(): string { return 'fa-mobile-category-menu'; }
    public function get_title(): string { return __('منوی دسته‌بندی موبایل فندق', 'fandoogh'); }
    public function get_icon(): string { return 'eicon-menu-bar'; }
    public function get_style_depends(): array { return ['fa-mobile-category-menu']; }
    public function get_script_depends(): array { return ['fa-mobile-category-menu']; }

    protected function registerWidgetControls(): void
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'fandoogh')]);
        $this->add_control('home_label', ['label' => __('عنوان خانه در مسیر', 'fandoogh'), 'type' => Controls_Manager::TEXT, 'default' => __('خانه', 'fandoogh')]);
        $this->add_control('category_ids', ['label' => __('دسته‌های اصلی نمایشی', 'fandoogh'), 'type' => Controls_Manager::SELECT2, 'multiple' => true, 'options' => self::categoryOptions(), 'description' => __('خالی بگذارید تا انتخاب پنل افزونه استفاده شود.', 'fandoogh')]);
        $this->end_controls_section();

        $this->start_controls_section('style', ['label' => __('استایل منوی موبایل', 'fandoogh'), 'tab' => Controls_Manager::TAB_STYLE]);
        $colors = [
            'mobile_bg' => ['پس‌زمینه', '--fa-mobile-nav-bg'], 'mobile_text_color' => ['رنگ متن', '--fa-mobile-nav-color'], 'mobile_border_color' => ['رنگ حاشیه', '--fa-mobile-nav-border'],
            'mobile_item_bg' => ['پس‌زمینه آیتم', '--fa-mobile-nav-item-bg'], 'mobile_item_hover_bg' => ['پس‌زمینه هاور', '--fa-mobile-nav-item-hover-bg'], 'mobile_item_hover_color' => ['رنگ متن هاور', '--fa-mobile-nav-item-hover-color'],
            'mobile_item_active_bg' => ['پس‌زمینه حالت انتخاب‌شده', '--fa-mobile-nav-item-active-bg'], 'mobile_item_active_color' => ['رنگ متن حالت انتخاب‌شده', '--fa-mobile-nav-item-active-color'], 'mobile_icon_color' => ['رنگ آیکون و فلش', '--fa-mobile-nav-icon-color'],
        ];
        foreach ($colors as $key => [$label, $variable]) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .fa-mobile-nav' => $variable . ': {{VALUE}} !important;']]);
        }
        $sizes = [
            'mobile_image_size' => ['اندازه تصویر دسته', '--fa-mobile-nav-image-size'], 'mobile_image_radius' => ['شعاع تصویر دسته', '--fa-mobile-nav-image-radius'], 'mobile_item_radius' => ['شعاع آیتم', '--fa-mobile-nav-item-radius'], 'mobile_item_padding' => ['پدینگ آیتم', '--fa-mobile-nav-item-padding'], 'mobile_item_gap' => ['فاصله تصویر و متن', '--fa-mobile-nav-item-gap'], 'mobile_font_size' => ['اندازه فونت', '--fa-mobile-nav-font-size'],
        ];
        foreach ($sizes as $key => [$label, $variable]) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'selectors' => ['{{WRAPPER}} .fa-mobile-nav' => $variable . ': {{VALUE}}px !important;']]);
        }
        $this->end_controls_section();
    }

    protected function renderWidget(): void
    {
        $settings = $this->get_settings_for_display();
        echo MobileRenderer::render([
            'home_label' => sanitize_text_field((string) ($settings['home_label'] ?? '')),
            'category_ids' => array_values(array_filter(array_map('absint', (array) ($settings['category_ids'] ?? [])))),
        ]);
    }

    /** @return array<int, string> */
    private static function categoryOptions(): array
    {
        $options = [];
        foreach (Repository::parentCategories(false) as $category) {
            $options[$category->term_id] = $category->name;
        }
        return $options;
    }
}
