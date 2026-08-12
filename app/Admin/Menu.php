<?php

declare(strict_types=1);

namespace Fandoogh\Admin;

defined('ABSPATH') || exit;

final class Menu
{
    public function register(): void
    {
        add_menu_page(__('فندق', 'fandoogh'), __('فندق', 'fandoogh'), 'manage_options', 'fa', [$this, 'dashboard'], 'dashicons-screenoptions', 58);
        add_submenu_page('fa', __('پیشخوان', 'fandoogh'), __('پیشخوان', 'fandoogh'), 'manage_options', 'fa', [$this, 'dashboard']);
        add_submenu_page('fa', __('ماژول سئو دسته‌بندی محصولات', 'fandoogh'), __('ماژول سئو دسته‌بندی محصولات', 'fandoogh'), 'manage_options', 'fa-modules', [$this, 'modules']);
        add_submenu_page('fa', __('ماژول سئو محصول', 'fandoogh'), __('ماژول سئو محصول', 'fandoogh'), 'manage_options', 'fa-product-seo', [$this, 'productSeo']);
        add_submenu_page('fa', __('ماشین حساب', 'fandoogh'), __('ماشین حساب', 'fandoogh'), 'manage_woocommerce', 'fa-calculator', [$this, 'calculator']);
        add_submenu_page('fa', __('مدیریت CRM', 'fandoogh'), __('مدیریت CRM', 'fandoogh'), 'manage_options', 'fa-crm', [$this, 'crm']);
        add_submenu_page('fa', __('پوسته پنل', 'fandoogh'), __('پوسته پنل', 'fandoogh'), 'manage_options', 'fa-theme', [$this, 'theme']);
        add_submenu_page('fa', __('تنظیمات', 'fandoogh'), __('تنظیمات', 'fandoogh'), 'manage_options', 'fa-settings', [$this, 'settings']);
        add_submenu_page('fa', __('پشتیبانی', 'fandoogh'), __('پشتیبانی', 'fandoogh'), 'manage_options', 'fa-support', [$this, 'support']);
    }

    public function dashboard(): void { Dashboard::render(); }
    public function modules(): void { Dashboard::render(); }
    public function productSeo(): void { Dashboard::render(); }
    public function calculator(): void { Dashboard::render(); }
    public function crm(): void { Dashboard::render(); }
    public function theme(): void { Dashboard::render(); }
    public function settings(): void { Dashboard::render(); }
    public function support(): void { Dashboard::render(); }
}
