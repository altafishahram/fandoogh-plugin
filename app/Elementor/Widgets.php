<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

use Elementor\Widgets_Manager;
use Fandoogh\Managers\ModuleManager;

defined('ABSPATH') || exit;

/**
 * Elementor Widgets.
 *
 * Registers all Fandoogh widgets.
 *
 * @package Fandoogh\Elementor
 */
final class Widgets
{
    public function __construct(
        private readonly ModuleManager $modules
    ) {
    }

    /**
     * Boot.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'elementor/widgets/register',
            [$this, 'register']
        );
    }

    /**
     * Register widgets.
     *
     * @param Widgets_Manager $manager
     *
     * @return void
     */
    public function register(
        Widgets_Manager $manager
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        if ($this->modules->enabled('description')) {
            $manager->register(
                new \Fandoogh\Modules\Description\Widget()
            );
        }
        /*
        |--------------------------------------------------------------------------
        | Video
        |--------------------------------------------------------------------------
        */

        if ($this->modules->enabled('video')) {
            $manager->register(
                new \Fandoogh\Modules\Video\Widget()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FAQ
        |--------------------------------------------------------------------------
        */

        if ($this->modules->enabled('faq')) {
            $manager->register(
                new \Fandoogh\Modules\Faq\Widget()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

        if ($this->modules->enabled('reviews')) {
            $manager->register(
                new \Fandoogh\Modules\Reviews\Widget()
            );
        }

    }
}
