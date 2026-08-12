<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

use Elementor\Elements_Manager;

defined('ABSPATH') || exit;

/**
 * Elementor Categories.
 *
 * Registers custom widget categories.
 *
 * @package Fandoogh\Elementor
 */
final class Categories
{
    /**
     * Boot.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'elementor/elements/categories_registered',
            [$this, 'register']
        );
    }

    /**
     * Register widget category.
     *
     * @param Elements_Manager $manager
     *
     * @return void
     */
    public function register(
        Elements_Manager $manager
    ): void {

        $manager->add_category(
            'fandoogh',
            [
                'title' => __('Fandoogh', 'fandoogh'),
                'icon'  => 'fa fa-plug',
            ]
        );
    }
}