<?php

declare(strict_types=1);

namespace Fandoogh\Admin;

defined('ABSPATH') || exit;

final class Dashboard
{
    public static function render(): void
    {
        $page = sanitize_key(wp_unslash($_GET['page'] ?? 'fa'));
        $default = match ($page) {
            'fa-modules' => 'modules',
            'fa-product-seo' => 'product_seo',
            'fa-calculator' => 'calculator',
            'fa-crm' => 'crm',
            'fa-theme' => 'theme',
            'fa-settings' => 'settings',
            'fa-support' => 'support',
            default => 'dashboard',
        };
        $section = sanitize_key(wp_unslash($_GET['section'] ?? $default));
        if (! in_array($section, ['dashboard', 'modules', 'product_seo', 'calculator', 'crm', 'theme', 'settings', 'support'], true)) {
            $section = 'dashboard';
        }

        $items = [
            'dashboard' => ['پیشخوان', 'dashicons-dashboard', 'fa'],
            'modules' => ['ماژول سئو دسته‌بندی محصولات', 'dashicons-admin-site-alt3', 'fa-modules'],
            'product_seo' => ['ماژول سئو محصول', 'dashicons-products', 'fa-product-seo'],
            'calculator' => ['ماشین حساب', 'dashicons-calculator', 'fa-calculator'],
            'crm' => ['مدیریت CRM', 'dashicons-groups', 'fa-crm'],
            'theme' => ['پوسته پنل', 'dashicons-art', 'fa-theme'],
            'settings' => ['تنظیمات', 'dashicons-admin-generic', 'fa-settings'],
            'support' => ['پشتیبانی', 'dashicons-heart', 'fa-support'],
        ];
        ?>
        <div class="wrap fa-admin-shell" dir="rtl">
            <aside class="fa-admin-sidebar">
                <div class="fa-admin-brand">
                    <img src="<?php echo esc_url(FA_URL . 'assets/admin/images/logo.webp'); ?>" alt="<?php esc_attr_e('فندق', 'fandoogh'); ?>">
                </div>
                <nav class="fa-admin-nav" aria-label="<?php esc_attr_e('ناوبری فندق', 'fandoogh'); ?>">
                    <?php foreach ($items as $key => [$label, $icon, $slug]) : ?>
                        <a class="fa-admin-nav-link <?php echo $section === $key ? 'is-active' : ''; ?>" data-section="<?php echo esc_attr($key); ?>" href="<?php echo esc_url(admin_url('admin.php?page=' . $slug)); ?>" <?php echo $section === $key ? 'aria-current="page"' : ''; ?>>
                            <span class="dashicons <?php echo esc_attr($icon); ?>" aria-hidden="true"></span><?php echo esc_html($label); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <div class="fa-admin-version">نسخه <?php echo esc_html(FA_VERSION); ?></div>
            </aside>
            <main id="fa-admin-content" class="fa-admin-content" data-section="<?php echo esc_attr($section); ?>" aria-live="polite"><?php echo Sections::render($section); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></main>
        </div>
        <?php
    }
}
