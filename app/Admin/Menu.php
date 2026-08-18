<?php

declare(strict_types=1);

namespace Fandoogh\Admin;

use Fandoogh\Core\Application;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\Modules\OrderCenter\Module as OrderCenterModule;

defined('ABSPATH') || exit;

final class Menu
{
    public function register(): void
    {
        add_menu_page(__('فندق', 'fandoogh'), __('فندق', 'fandoogh'), 'manage_options', 'fa', [$this, 'dashboard'], 'dashicons-screenoptions', 58);
        add_submenu_page('fa', __('پیشخوان', 'fandoogh'), __('پیشخوان', 'fandoogh'), 'manage_options', 'fa', [$this, 'dashboard']);
        add_submenu_page('fa', __('ماژول سئو دسته‌بندی محصولات', 'fandoogh'), __('ماژول سئو دسته‌بندی محصولات', 'fandoogh'), 'manage_options', 'fa-modules', [$this, 'modules']);
        add_submenu_page('fa', __('ماژول سئو محصول', 'fandoogh'), __('ماژول سئو محصول', 'fandoogh'), 'manage_options', 'fa-product-seo', [$this, 'productSeo']);
        $modules = Application::instance()->get('modules');
        if ($modules instanceof ModuleManager && $modules->enabled('order-center') && class_exists('Fandoogh\\Modules\\OrderCenter\\Module') && \Fandoogh\Modules\OrderCenter\Module::isAvailable()) {
            add_menu_page(__('مرکز سفارشات', 'fandoogh'), __('مرکز سفارشات', 'fandoogh'), 'manage_woocommerce', 'fa-order-center', [$this, 'orderCenter'], 'dashicons-cart', 57);
        }
        add_submenu_page('fa', __('ماشین حساب', 'fandoogh'), __('ماشین حساب', 'fandoogh'), 'manage_woocommerce', 'fa-calculator', [$this, 'calculator']);
        add_submenu_page('fa', __('مدیریت CRM', 'fandoogh'), __('مدیریت CRM', 'fandoogh'), 'manage_options', 'fa-crm', [$this, 'crm']);
        add_submenu_page('fa', __('پوسته پنل', 'fandoogh'), __('پوسته پنل', 'fandoogh'), 'manage_options', 'fa-theme', [$this, 'theme']);
        add_submenu_page('fa', __('تنظیمات', 'fandoogh'), __('تنظیمات', 'fandoogh'), 'manage_options', 'fa-settings', [$this, 'settings']);
        add_submenu_page('fa', __('پشتیبانی', 'fandoogh'), __('پشتیبانی', 'fandoogh'), 'manage_options', 'fa-support', [$this, 'support']);
    }

    public function dashboard(): void { Dashboard::render(); }
    public function modules(): void { Dashboard::render(); }
    public function productSeo(): void { Dashboard::render(); }
    public function orderCenter(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('دسترسی مدیریت سفارش‌ها را ندارید.', 'دسترسی غیرمجاز', ['response' => 403]);
        }

        echo '<div class="wrap fa-order-center-standalone" dir="rtl">';
        OrderCenterModule::render();
        echo '</div>';
    }
    public function calculator(): void { Dashboard::render(); }
    public function crm(): void { Dashboard::render(); }
    public function theme(): void { Dashboard::render(); }
    public function settings(): void { Dashboard::render(); }
    public function support(): void { Dashboard::render(); }
}
