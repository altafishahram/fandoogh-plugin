<?php

declare(strict_types=1);

namespace Fandoogh\Projects;

defined('ABSPATH') || exit;

final class Application
{
    public function boot(): void
    {
        (new Admin())->boot();

        (new PostType())->register();

        (new Taxonomy())->register();

        (new MetaBoxes())->boot();

        (new Shortcodes())->boot();
    }
}
