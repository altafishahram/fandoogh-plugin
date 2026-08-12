<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Description;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Fandoogh\Elementor\Widget as BaseWidget;

defined('ABSPATH') || exit;

/**
 * Description Widget.
 *
 * Displays the custom category description.
 *
 * @package Fandoogh\Modules\Description
 */
final class Widget extends BaseWidget
{
    /**
     * Widget Name.
     */
    public function get_name(): string
    {
        return 'fa-description';
    }

    /**
     * Widget Title.
     */
    public function get_title(): string
    {
        return __('توضیحات دسته‌بندی', 'fandoogh');
    }

    /**
     * Widget Icon.
     */
    public function get_icon(): string
    {
        return 'eicon-editor-align-left';
    }

    /**
     * Register Controls.
     */
    protected function registerWidgetControls(): void
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('محتوا', 'fandoogh'),
            ]
        );

        $this->add_control(
            'empty_message',
            [
                'label'       => __('پیام محتوای خالی', 'fandoogh'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('توضیحاتی یافت نشد.', 'fandoogh'),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section('section_style', [
            'label' => __('ظاهر', 'fandoogh'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('text_color', [
            'label' => __('رنگ متن', 'fandoogh'), 'type' => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .fa-description-content' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'typography', 'selector' => '{{WRAPPER}} .fa-description-content',
        ]);
        $this->end_controls_section();
    }

    /**
     * Render Widget.
     */
    protected function renderWidget(): void
    {
        if (! $this->isProductCategory()) {
            return;
        }

        $description = Renderer::render($this->currentTermId());

        if ($description === '') {

            echo esc_html(
                (string) $this->get_settings_for_display('empty_message')
            );

            return;
        }

        $this->output($description);
    }
}
