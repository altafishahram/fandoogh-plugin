<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Video;

use Elementor\Controls_Manager;
use Fandoogh\Elementor\Widget as BaseWidget;

defined('ABSPATH') || exit;

/**
 * Video Widget.
 *
 * Displays the product category video.
 *
 * @package Fandoogh\Modules\Video
 */
final class Widget extends BaseWidget
{
    /**
     * Widget Name.
     */
    public function get_name(): string
    {
        return 'fa-video';
    }

    /**
     * Widget Title.
     */
    public function get_title(): string
    {
        return __('ویدئوی دسته‌بندی', 'fandoogh');
    }

    /**
     * Widget Icon.
     */
    public function get_icon(): string
    {
        return 'eicon-play';
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
            'controls',
            [
                'label'        => __('نمایش کنترل‌ها', 'fandoogh'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('بله', 'fandoogh'),
                'label_off'    => __('خیر', 'fandoogh'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'        => __('پخش خودکار', 'fandoogh'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('بله', 'fandoogh'),
                'label_off'    => __('خیر', 'fandoogh'),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

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

        $this->output(Renderer::render($this->currentTermId(), [
            'controls' => $this->get_settings_for_display('controls') === 'yes',
            'autoplay' => $this->get_settings_for_display('autoplay') === 'yes',
        ]));
    }
}
