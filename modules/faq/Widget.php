<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Fandoogh\Elementor\Widget as BaseWidget;

defined('ABSPATH') || exit;

/**
 * FAQ Widget.
 *
 * @package Fandoogh\Modules\Faq
 */
final class Widget extends BaseWidget
{
    /**
     * Widget name.
     */
    public function get_name(): string
    {
        return 'fa-faq';
    }

    /**
     * Widget title.
     */
    public function get_title(): string
    {
        return __('سؤالات متداول دسته‌بندی', 'fandoogh');
    }

    /**
     * Widget icon.
     */
    public function get_icon(): string
    {
        return 'eicon-accordion';
    }

    /**
     * Register controls.
     */
    protected function registerWidgetControls(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('محتوا', 'fandoogh'),
            ]
        );

        $this->add_control(
            'first_open',
            [
                'label' => __('بازبودن سؤال اول', 'fandoogh'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'empty_message',
            [
                'label' => __('پیام فهرست خالی', 'fandoogh'),
                'type' => Controls_Manager::TEXT,
                'default' => __('سؤالی یافت نشد.', 'fandoogh'),
            ]
        );

        $this->end_controls_section();

        /*
        |--------------------------------------------------------------------------
        | Question Style
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_question_style',
            [
                'label' => __('سؤال', 'fandoogh'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'question_color',
            [
                'label' => __('رنگ', 'fandoogh'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fa-faq-question' =>
                        'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'question_typography',
                'selector' =>
                    '{{WRAPPER}} .fa-faq-question',
            ]
        );

        $this->end_controls_section();

        /*
        |--------------------------------------------------------------------------
        | Answer Style
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_answer_style',
            [
                'label' => __('پاسخ', 'fandoogh'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'answer_color',
            [
                'label' => __('رنگ', 'fandoogh'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .fa-faq-answer' =>
                        'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'answer_typography',
                'selector' =>
                    '{{WRAPPER}} .fa-faq-answer',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget.
     */
    protected function renderWidget(): void
    {
        if (! $this->isProductCategory()) {
            return;
        }

        $html = Renderer::render(
            $this->currentTermId(),
            $this->setting('first_open') === 'yes'
        );

        if ($html === '') {

            $this->emptyMessage(
                (string) $this->setting(
                    'empty_message'
                )
            );

            return;
        }

        $this->output($html);
    }
}
