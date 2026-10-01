<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || exit;

final class Widget extends \Fandoogh\Elementor\Widget
{
    public function get_name(): string { return 'fa-partner-price'; }
    public function get_title(): string { return __('قیمت همکاری فندق', 'fandoogh'); }
    public function get_icon(): string { return 'eicon-product-price'; }
    public function get_style_depends(): array { return ['fa-partner-price']; }

    protected function registerWidgetControls(): void
    {
        $this->start_controls_section('product', ['label' => __('محصول و نمایش', 'fandoogh')]);
        $this->add_control('product_id', [
            'label' => __('شناسه محصول یا تنوع', 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0,
            'description' => __('خالی یا صفر: محصول جاری. برای نمایش در هر برگه، شناسه محصول را وارد کنید. فقط همکاران واردشده خروجی را می‌بینند.', 'fandoogh'),
        ]);
        $this->add_control('label', ['label' => __('عنوان', 'fandoogh'), 'type' => Controls_Manager::TEXT, 'default' => __('قیمت همکاری', 'fandoogh')]);
        $this->add_control('show_cart', ['label' => __('دکمه خرید / انتخاب گزینه‌ها', 'fandoogh'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->end_controls_section();

        foreach ([
            'label' => [__('عنوان قیمت', 'fandoogh'), '.fa-partner-price__label'],
            'amount' => [__('مبلغ', 'fandoogh'), '.fa-partner-price__amount'],
            'buy' => [__('دکمه خرید', 'fandoogh'), '.fa-partner-price__buy'],
        ] as $key => [$title, $selector]) {
            $this->start_controls_section('style_' . $key, ['label' => $title, 'tab' => Controls_Manager::TAB_STYLE]);
            $this->add_control($key . '_color', ['label' => __('رنگ متن', 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} ' . $selector => 'color: {{VALUE}};']]);
            $this->add_group_control(Group_Control_Typography::get_type(), ['name' => $key . '_typography', 'selector' => '{{WRAPPER}} ' . $selector]);
            if ($key === 'buy') {
                $this->add_control('buy_bg', ['label' => __('پس‌زمینه', 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} ' . $selector => 'background-color: {{VALUE}};']]);
                $this->add_control('buy_hover_bg', ['label' => __('پس‌زمینه هاور', 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} ' . $selector . ':hover' => 'background-color: {{VALUE}};']]);
                $this->add_control('buy_hover_color', ['label' => __('رنگ متن هاور', 'fandoogh'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} ' . $selector . ':hover' => 'color: {{VALUE}};']]);
                $this->add_control('buy_radius', ['label' => __('گردی گوشه‌ها', 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'selectors' => ['{{WRAPPER}} ' . $selector => 'border-radius: {{VALUE}}px;']]);
            }
            $this->end_controls_section();
        }
        $this->start_controls_section('layout', ['label' => __('چیدمان', 'fandoogh'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('تراز', 'fandoogh'), 'type' => Controls_Manager::SELECT, 'options' => ['start' => __('ابتدا', 'fandoogh'), 'center' => __('وسط', 'fandoogh'), 'end' => __('انتها', 'fandoogh')], 'selectors' => ['{{WRAPPER}} .fa-partner-price' => 'text-align: {{VALUE}}; align-items: {{VALUE}};']]);
        $this->add_responsive_control('gap', ['label' => __('فاصله اجزا', 'fandoogh'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'selectors' => ['{{WRAPPER}} .fa-partner-price' => 'gap: {{VALUE}}px;']]);
        $this->end_controls_section();
    }

    protected function renderWidget(): void
    {
        // Display escapes the content and applies the same authorization as the shortcode.
        echo Display::render($this->get_settings_for_display());
        if (! Access::isPartner() && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<p>' . esc_html__('قیمت همکاری تنها برای همکار واردشده نمایش داده می‌شود. محصول را انتخاب کنید و خروجی را با حساب همکار بررسی کنید.', 'fandoogh') . '</p>';
        }
    }
}
