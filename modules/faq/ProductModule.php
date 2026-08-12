<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Fandoogh\Managers\ModuleManager;

defined('ABSPATH') || exit;

/**
 * Boots the independently configurable product SEO features.
 */
final class ProductModule
{
    public function __construct(
        private readonly ModuleManager $modules
    ) {
    }

    public function boot(): void
    {
        $faqEnabled = $this->modules->enabled('product_faq');
        $reasonEnabled = $this->modules->enabled('product_reason');

        if (! $faqEnabled && ! $reasonEnabled) {
            return;
        }

        if (is_admin()) {
            (new ProductAdmin($faqEnabled, $reasonEnabled))->boot();
        }

        (new ProductFrontend($faqEnabled))->boot();
        (new ProductShortcodes($faqEnabled, $reasonEnabled))->boot();
        (new ProductSchema($reasonEnabled))->boot();
    }
}
