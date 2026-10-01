<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

defined('ABSPATH') || exit;

final class Module
{
    public function boot(): void
    {
        if (! function_exists('wc_get_product')) {
            return;
        }

        (new Admin())->boot();
        (new Pricing())->boot();
        add_shortcode('fa_partner_price', [Display::class, 'shortcode']);
        add_action('wp_enqueue_scripts', [Display::class, 'assets']);
    }
}
