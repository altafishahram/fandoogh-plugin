<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Elementor\Controls_Manager;
use Fandoogh\Elementor\Widget as BaseWidget;

defined('ABSPATH') || exit;

final class Widget extends BaseWidget
{
    public function get_name(): string
    {
        return 'fa-reviews';
    }

    public function get_title(): string
    {
        return __('نظرات دسته‌بندی', 'fandoogh');
    }

    public function get_icon(): string
    {
        return 'eicon-rating';
    }

    protected function registerWidgetControls(): void
    {
        $this->start_controls_section(
            'content',
            [
                'label' => __('محتوا', 'fandoogh'),
            ]
        );

        $this->add_control(
            'show_summary',
            [
                'label' => __('نمایش خلاصه امتیازها', 'fandoogh'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_list',
            [
                'label' => __('نمایش نظرات', 'fandoogh'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_form',
            [
                'label' => __('نمایش فرم ثبت نظر', 'fandoogh'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('style', [
            'label' => __('ظاهر', 'fandoogh'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('accent_color', [
            'label' => __('رنگ اصلی', 'fandoogh'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .fa-star.active' => 'color: {{VALUE}};',
                '{{WRAPPER}} .fa-review-form button' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
            ],
        ]);
        $this->end_controls_section();
    }

    protected function renderWidget(): void
    {
        if (! $this->isProductCategory()) {
            return;
        }

        $termId = $this->currentTermId();

        if ($termId <= 0) {
            return;
        }

        Frontend::render(
            $termId,
            [
                'summary' => $this->setting('show_summary') === 'yes',
                'list'    => $this->setting('show_list') === 'yes',
                'form'    => $this->setting('show_form') === 'yes',
            ]
        );
    }
}
