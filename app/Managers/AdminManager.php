<?php

declare(strict_types=1);

namespace Fandoogh\Managers;

use Fandoogh\Admin\Ajax\DashboardAjax;
use Fandoogh\Admin\Ajax\ModulesAjax;
use Fandoogh\Admin\Ajax\SettingsAjax;
use Fandoogh\Admin\Ajax\ThemeAjax;
use Fandoogh\AdminTheme\SettingsSchema;
use Fandoogh\AdminTheme\ThemeManager;
use Fandoogh\Admin\Menu;
use Fandoogh\Core\Constants\Assets;
use Fandoogh\Calculator\AdminAssets as CalculatorAdminAssets;

defined('ABSPATH') || exit;

final class AdminManager
{
    public function boot(): void
    {
        add_action('admin_menu', [(new Menu()), 'register']);
        (new ModulesAjax())->boot();
        (new DashboardAjax())->boot();
        (new SettingsAjax())->boot();
        (new ThemeAjax())->boot();
        add_action('admin_enqueue_scripts', [$this, 'assets']);
    }

    public function assets(): void
    {
        $page = sanitize_key(wp_unslash($_GET['page'] ?? ''));
        $pages = ['fa', 'fa-modules', 'fa-product-seo', 'fa-calculator', 'fa-crm', 'fa-theme', 'fa-settings', 'fa-support'];
        if (! in_array($page, $pages, true)) {
            return;
        }

        wp_enqueue_style(Assets::ADMIN, FA_URL . Assets::ADMIN_CSS, [], FA_BUILD);
        wp_enqueue_script(Assets::ADMIN_DASHBOARD, FA_URL . Assets::ADMIN_DASHBOARD_JS, ['jquery'], FA_BUILD, true);
        wp_localize_script(Assets::ADMIN_DASHBOARD, 'faAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fa_admin'),
            'urls' => [
                'dashboard' => admin_url('admin.php?page=fa'),
                'modules' => admin_url('admin.php?page=fa-modules'),
                'product_seo' => admin_url('admin.php?page=fa-product-seo'),
                'calculator' => admin_url('admin.php?page=fa-calculator'),
                'crm' => admin_url('admin.php?page=fa-crm'),
                'theme' => admin_url('admin.php?page=fa-theme'),
                'settings' => admin_url('admin.php?page=fa-settings'),
                'support' => admin_url('admin.php?page=fa-support'),
            ],
        ]);
        wp_enqueue_style(Assets::ADMIN_MODULES, FA_URL . Assets::ADMIN_MODULES_CSS, [Assets::ADMIN], FA_BUILD);
        wp_enqueue_script(Assets::ADMIN_MODULES, FA_URL . Assets::ADMIN_MODULES_JS, ['jquery'], FA_BUILD, true);
        wp_localize_script(Assets::ADMIN_MODULES, 'faModules', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fa_modules'),
        ]);

        (new CalculatorAdminAssets())->enqueue();

        wp_enqueue_script(
            Assets::ADMIN_THEME_MANAGER,
            FA_URL . Assets::ADMIN_THEME_MANAGER_JS,
            ['jquery', Assets::ADMIN_DASHBOARD],
            FA_BUILD,
            true
        );
        wp_localize_script(Assets::ADMIN_THEME_MANAGER, 'faTheme', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fa_admin'),
            'presets' => SettingsSchema::presets(),
        ]);

        $theme = new ThemeManager();
        $theme->ensure();
        $theme->enqueue();
    }
}
