<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Frontend
{
    public function boot(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
    }

    public function registerAssets(): void
    {
        wp_register_style('fa-mega-menu', FA_URL . 'modules/mega-menu/Assets/css/mega-menu.css', [], FA_VERSION);
        wp_register_script('fa-mega-menu', FA_URL . 'modules/mega-menu/Assets/js/mega-menu.js', [], FA_VERSION, true);
        wp_register_style('fa-mobile-category-menu', FA_URL . 'modules/mega-menu/Assets/css/mobile-category-menu.css', [], FA_VERSION);
        wp_register_script('fa-mobile-category-menu', FA_URL . 'modules/mega-menu/Assets/js/mobile-category-menu.js', [], FA_VERSION, true);
        wp_enqueue_style('fa-mega-menu');
        wp_enqueue_script('fa-mega-menu');
        wp_enqueue_style('fa-mobile-category-menu');
        wp_enqueue_script('fa-mobile-category-menu');
    }

    public static function enqueue(): void
    {
        if (! wp_style_is('fa-mega-menu', 'registered')) (new self())->registerAssets();
        wp_enqueue_style('fa-mega-menu');
        wp_enqueue_script('fa-mega-menu');
    }

    public static function enqueueMobile(): void
    {
        if (! wp_style_is('fa-mobile-category-menu', 'registered')) {
            (new self())->registerAssets();
        }
        wp_enqueue_style('fa-mobile-category-menu');
        wp_enqueue_script('fa-mobile-category-menu');
    }
}
