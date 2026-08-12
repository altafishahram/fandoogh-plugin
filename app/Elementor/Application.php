<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

use Fandoogh\Managers\ModuleManager;

defined('ABSPATH') || exit;

final class Application
{
    private bool $booted = false;

    public function __construct(private readonly ModuleManager $modules)
    {
    }

    public function boot(): void
    {
        if (Support::isLoaded()) {
            $this->bootIntegration();
            return;
        }

        add_action('elementor/loaded', [$this, 'bootIntegration']);
    }

    public function bootIntegration(): void
    {
        if ($this->booted || ! Support::isCompatible()) {
            return;
        }

        $this->booted = true;
        (new Categories())->boot();
        (new DynamicTags($this->modules))->boot();
        (new Widgets($this->modules))->boot();
    }
}
