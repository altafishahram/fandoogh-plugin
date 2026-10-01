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
        wp_register_style('fa-mega-menu', FA_URL . 'modules/mega-menu/Assets/css/mega-menu.css', [], self::assetVersion('modules/mega-menu/Assets/css/mega-menu.css'));
        wp_register_style('fa-mega-menu-layout', FA_URL . 'modules/mega-menu/Assets/css/mega-menu-layout.css', ['fa-mega-menu'], self::assetVersion('modules/mega-menu/Assets/css/mega-menu-layout.css'));
        wp_register_script('fa-mega-menu', FA_URL . 'modules/mega-menu/Assets/js/mega-menu.js', [], self::assetVersion('modules/mega-menu/Assets/js/mega-menu.js'), true);
        wp_register_style('fa-mobile-category-menu', FA_URL . 'modules/mega-menu/Assets/css/mobile-category-menu.css', [], self::assetVersion('modules/mega-menu/Assets/css/mobile-category-menu.css'));
        wp_register_script('fa-mobile-category-menu', FA_URL . 'modules/mega-menu/Assets/js/mobile-category-menu.js', [], self::assetVersion('modules/mega-menu/Assets/js/mobile-category-menu.js'), true);
        wp_enqueue_style('fa-mega-menu');
        wp_enqueue_style('fa-mega-menu-layout');
        wp_enqueue_script('fa-mega-menu');
        wp_enqueue_style('fa-mobile-category-menu');
        wp_enqueue_script('fa-mobile-category-menu');
    }

    public static function enqueue(): void
    {
        if (! wp_style_is('fa-mega-menu', 'registered')) (new self())->registerAssets();
        wp_enqueue_style('fa-mega-menu');
        wp_enqueue_style('fa-mega-menu-layout');
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

    private static function assetVersion(string $path): string
    {
        $file = FA_PATH . $path;
        return is_file($file) ? (string) filemtime($file) : FA_VERSION;
    }
}
