<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

use Elementor\Controls_Manager;
use Fandoogh\Elementor\Widget;

defined('ABSPATH') || exit;

final class ElementorWidget extends Widget
{
    public function get_name(): string { return 'fa-mega-menu'; }
    public function get_title(): string { return __('مگا منو فندق', 'fandoogh'); }
    public function get_icon(): string { return 'eicon-nav-menu'; }
    public function get_style_depends(): array { return ['fa-mega-menu']; }

    protected function registerWidgetControls(): void
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'fandoogh')]);
        $this->add_control('button_text', ['label' => __('متن دکمه', 'fandoogh'), 'type' => Controls_Manager::TEXT, 'default' => __('دسته‌بندی محصولات', 'fandoogh')]);
        $this->add_control('button_icon', ['label' => __('آیکون دکمه', 'fandoogh'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('پیش‌فرض ماژول', 'fandoogh')] + Service::buttonIcons()]);
        $this->add_control('interaction_mode', ['label' => __('نحوه بازشدن منو', 'fandoogh'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('پیش‌فرض ماژول', 'fandoogh'), 'hover' => __('هاور', 'fandoogh'), 'click' => __('کلیک', 'fandoogh')], 'selectors' => ['{{WRAPPER}} .fa-mega-wrapper' => '--fa-mega-interaction-mode: {{VALUE}} !important;']]);
        $this->end_controls_section();

        $this->start_controls_section('style', ['label' => __('استایل', 'fandoogh'), 'tab' => Controls_Manager::TAB_STYLE]);
        $colorControls = [
            'button_bg' => ['رنگ دکمه', '--fa-mega-button-bg'], 'button_color' => ['رنگ متن دکمه', '--fa-mega-button-color'], 'button_hover' => ['رنگ هاور دکمه', '--fa-mega-button-hover'], 'button_active' => ['رنگ فعال دکمه', '--fa-mega-button-active'],
            'menu_bg' => ['پس‌زمینه منو', '--fa-mega-menu-bg'], 'sidebar_bg' => ['پس‌زمینه سایدبار', '--fa-mega-sidebar-bg'], 'sidebar_color' => ['رنگ متن سایدبار', '--fa-mega-sidebar-color'], 'sidebar_hover' => ['رنگ هاور سایدبار', '--fa-mega-sidebar-hover'], 'sidebar_hover_bg' => ['پس‌زمینه هاور سایدبار', '--fa-mega-sidebar-hover-bg'], 'sidebar_active' => ['رنگ متن فعال سایدبار', '--fa-mega-sidebar-active'], 'sidebar_active_bg' => ['پس‌زمینه فعال سایدبار', '--fa-mega-sidebar-active-bg'],
            'card_border' => ['حاشیه کارت', '--fa-mega-card-border'], 'card_hover_border' => ['حاشیه هاور کارت', '--fa-mega-card-hover-border'], 'card_icon_bg' => ['پس‌زمینه آیکون کارت', '--fa-mega-card-icon-bg'], 'banner_background' => ['رنگ بنر', '--fa-mega-banner-bg'],
        ];
        foreach ($colorControls as $key => [$label, $variable]) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .fa-mega-wrapper' => $variable . ': {{VALUE}} !important;']]);
        }
        $sizeControls = [
            'button_radius' => ['شعاع دکمه', '--fa-mega-button-radius'], 'button_padding' => ['پدینگ دکمه', '--fa-mega-button-padding'], 'button_font_size' => ['اندازه فونت دکمه', '--fa-mega-button-font-size'],
            'menu_width' => ['عرض منو', '--fa-mega-width'], 'sidebar_width' => ['عرض سایدبار', '--fa-mega-sidebar-width'], 'menu_radius' => ['شعاع منو', '--fa-mega-radius'],
            'sidebar_font_size' => ['اندازه فونت سایدبار', '--fa-mega-sidebar-font-size'], 'sidebar_padding' => ['پدینگ آیتم سایدبار', '--fa-mega-sidebar-padding'],
            'card_radius' => ['شعاع کارت', '--fa-mega-card-radius'], 'card_title_size' => ['اندازه عنوان کارت', '--fa-mega-card-title-size'], 'card_count_size' => ['اندازه تعداد محصول', '--fa-mega-card-count-size'], 'image_size' => ['اندازه تصویر دسته', '--fa-mega-image-size'],
            'banner_height' => ['ارتفاع بنر', '--fa-mega-banner-height'], 'banner_radius' => ['شعاع بنر', '--fa-mega-banner-radius'],
        ];
        foreach ($sizeControls as $key => [$label, $variable]) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'selectors' => ['{{WRAPPER}} .fa-mega-wrapper' => $variable . ': {{VALUE}}px !important;']]);
        }
        foreach (['button_shadow' => ['سایه دکمه', '--fa-mega-button-shadow'], 'menu_shadow' => ['سایه منو', '--fa-mega-shadow'], 'card_hover_shadow' => ['سایه هاور کارت', '--fa-mega-card-hover-shadow'], 'banner_gradient' => ['گرادیان یا پس‌زمینه بنر', '--fa-mega-banner-bg']] as $key => [$label, $variable]) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::TEXT, 'description' => __('نمونه: 0 8px 20px rgba(0,0,0,.15) یا linear-gradient(135deg,#7c4dff,#6200ea)', 'fandoogh'), 'selectors' => ['{{WRAPPER}} .fa-mega-wrapper' => $variable . ': {{VALUE}} !important;']]);
        }
        $this->add_control('grid_columns', ['label' => __('ستون‌های گرید', 'fandoogh'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('پیش‌فرض ماژول', 'fandoogh'), '2' => '2', '3' => '3', '4' => '4'], 'selectors' => ['{{WRAPPER}} .fa-mega-wrapper' => '--fa-mega-grid-columns: {{VALUE}} !important;']]);
        $this->end_controls_section();
    }

    protected function renderWidget(): void
    {
        $settings = $this->get_settings_for_display();
        $html = Renderer::render([
            'button_text' => sanitize_text_field((string) ($settings['button_text'] ?? '')),
            'button_icon' => sanitize_key((string) ($settings['button_icon'] ?? '')),
            'interaction_mode' => sanitize_key((string) ($settings['interaction_mode'] ?? '')),
        ]);
        // Renderer escapes its dynamic values itself; retain scoped CSS custom properties.
        echo $html;
    }
}
