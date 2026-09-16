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

    protected function registerWidgetControls(): void
    {
        $this->start_controls_section('content', ['label' => __('محتوا', 'fandoogh')]);
        $this->add_control('button_text', ['label' => __('متن دکمه', 'fandoogh'), 'type' => Controls_Manager::TEXT, 'default' => __('دسته‌بندی محصولات', 'fandoogh')]);
        $this->end_controls_section();
        $this->start_controls_section('style', ['label' => __('استایل', 'fandoogh'), 'tab' => Controls_Manager::TAB_STYLE]);
        foreach (['button_bg' => 'رنگ دکمه', 'button_color' => 'رنگ متن دکمه', 'menu_bg' => 'پس‌زمینه منو', 'sidebar_bg' => 'پس‌زمینه سایدبار', 'sidebar_active' => 'رنگ فعال سایدبار', 'card_border' => 'حاشیه کارت'] as $key => $label) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::COLOR]);
        }
        foreach (['menu_width' => 'عرض منو', 'sidebar_width' => 'عرض سایدبار', 'image_size' => 'اندازه تصویر دسته', 'banner_height' => 'ارتفاع بنر'] as $key => $label) {
            $this->add_control($key, ['label' => __($label, 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0]);
        }
        $this->add_control('grid_columns', ['label' => __('ستون‌های گرید', 'fandoogh'), 'type' => Controls_Manager::SELECT, 'options' => ['2' => '2', '3' => '3', '4' => '4'], 'default' => '3']);
        $this->end_controls_section();
    }

    protected function renderWidget(): void
    {
        $settings = $this->get_settings_for_display();
        $styles = [];
        foreach (['button_bg', 'button_color', 'menu_bg', 'sidebar_bg', 'sidebar_active', 'card_border', 'menu_width', 'sidebar_width', 'image_size', 'banner_height', 'grid_columns'] as $key) if (($settings[$key] ?? '') !== '') $styles[$key] = $settings[$key];
        $html = Renderer::render(['button_text' => sanitize_text_field((string) ($settings['button_text'] ?? '')), 'styles' => $styles]);
        // Renderer escapes its dynamic values itself; retain scoped CSS custom properties.
        echo $html;
    }
}
