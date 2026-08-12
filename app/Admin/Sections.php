<?php

declare(strict_types=1);

namespace Fandoogh\Admin;

use Fandoogh\Core\Application;
use Fandoogh\Core\Constants\Options;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\AdminTheme\SettingsSchema;
use Fandoogh\AdminTheme\ThemeManager;

defined('ABSPATH') || exit;

final class Sections
{
    public static function render(string $section): string
    {
        ob_start();

        if ($section === 'modules') {
            self::modules();
        } elseif ($section === 'product_seo') {
            self::productSeo();
        } elseif ($section === 'calculator') {
            \Fandoogh\Calculator\AdminPage::render();
        } elseif ($section === 'crm') {
            self::crm();
        } elseif ($section === 'theme') {
            self::theme();
        } elseif ($section === 'settings') {
            self::settings();
        } elseif ($section === 'support') {
            self::support();
        } else {
            self::dashboard();
        }

        return (string) ob_get_clean();
    }

    private static function dashboard(): void
    {
        $modules = Application::instance()->get('modules');
        $enabled = $modules instanceof ModuleManager ? count(array_filter($modules->all())) : 0;
        $customers = wp_count_posts('fa_customer');
        $projects = wp_count_posts('fa_project');
        $comments = wp_count_comments();
        $stats = [
            ['مشتریان', (int) ($customers->publish ?? 0), 'dashicons-groups'],
            ['پروژه‌ها', (int) ($projects->publish ?? 0), 'dashicons-portfolio'],
            ['نظرات تأییدشده', (int) ($comments->approved ?? 0), 'dashicons-star-filled'],
            ['ماژول‌های فعال', $enabled, 'dashicons-admin-plugins'],
        ];
        $health = [
            ['وردپرس', get_bloginfo('version'), true],
            ['PHP', PHP_VERSION, version_compare(PHP_VERSION, '8.2', '>=')],
            ['ووکامرس', defined('WC_VERSION') ? WC_VERSION : 'غیرفعال', defined('WC_VERSION')],
            ['Elementor', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : 'غیرفعال', defined('ELEMENTOR_VERSION')],
        ];
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">پیشخوان فندق</h1><p>مدیریت یکپارچه قابلیت‌ها و وضعیت فریم‌ورک</p></div><span class="fa-admin-build">Build <?php echo esc_html(FA_BUILD); ?></span></header>
        <section class="fa-admin-stats" aria-label="آمار افزونه"><?php foreach ($stats as [$label, $value, $icon]) : ?><article class="fa-admin-stat"><span class="dashicons <?php echo esc_attr($icon); ?>" aria-hidden="true"></span><div><strong><?php echo esc_html((string) $value); ?></strong><small><?php echo esc_html($label); ?></small></div></article><?php endforeach; ?></section>
        <section class="fa-admin-grid"><article class="fa-panel"><header><span class="dashicons dashicons-shield" aria-hidden="true"></span><h2>سلامت سیستم</h2></header><div class="fa-health-list"><?php foreach ($health as [$label, $value, $ok]) : ?><div><span><?php echo esc_html($label); ?></span><b class="<?php echo $ok ? 'is-ok' : 'is-warning'; ?>"><?php echo esc_html((string) $value); ?></b></div><?php endforeach; ?></div></article><article class="fa-panel"><header><span class="dashicons dashicons-admin-links" aria-hidden="true"></span><h2>دسترسی سریع</h2></header><div class="fa-quick-links"><a href="<?php echo esc_url(admin_url('post-new.php?post_type=fa_customer')); ?>">افزودن مشتری</a><a href="<?php echo esc_url(admin_url('post-new.php?post_type=fa_project')); ?>">افزودن پروژه</a><a href="<?php echo esc_url(admin_url('edit-comments.php')); ?>">مدیریت نظرات</a><a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=product_cat&post_type=product')); ?>">دسته‌های محصول</a></div></article></section>
        <?php
    }

    private static function modules(): void
    {
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">ماژول سئو دسته‌بندی محصولات</h1><p>محتوای تکمیلی و تعاملی دسته‌های محصولات ووکامرس را مدیریت کنید.</p></div></header>
        <?php self::moduleCards(['description', 'video', 'faq', 'reviews']); ?>
        <?php
    }

    private static function productSeo(): void
    {
        ?>
        <header class="fa-admin-welcome">
            <div>
                <h1 tabindex="-1">ماژول سئو محصول</h1>
                <p>محتوای تکمیلی و داده‌های ساختاریافته هر محصول ووکامرس را مدیریت کنید.</p>
            </div>
        </header>
        <section class="fa-crm-intro fa-panel" aria-labelledby="fa-product-seo-intro-title">
            <header>
                <span class="dashicons dashicons-search" aria-hidden="true"></span>
                <h2 id="fa-product-seo-intro-title">امکانات سئو محتوایی محصول</h2>
            </header>
            <div class="fa-crm-features">
                <div>
                    <strong>سؤالات متداول محصول</strong>
                    <p>برای هر محصول FAQ اختصاصی، شورت‌کد، Dynamic Tag المنتور و FAQPage Schema اضافه می‌کند.</p>
                </div>
                <div>
                    <strong>پاسخ تک‌سؤالی محصول</strong>
                    <p>یک سؤال و پاسخ شاخص با ویرایشگر وردپرس، HTML امن و اتصال به Product Schema ووکامرس اضافه می‌کند.</p>
                </div>
            </div>
        </section>
        <?php self::moduleCards(['product_faq', 'product_reason']); ?>
        <?php
    }

    private static function crm(): void
    {
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">مدیریت CRM</h1><p>قابلیت‌های نمایش سوابق مشتریان و پروژه‌های انجام‌شده را فعال کنید.</p></div></header>
        <section class="fa-crm-intro fa-panel" aria-labelledby="fa-crm-intro-title">
            <header><span class="dashicons dashicons-groups" aria-hidden="true"></span><h2 id="fa-crm-intro-title">بعد از فعال‌سازی چه امکاناتی اضافه می‌شود؟</h2></header>
            <div class="fa-crm-features">
                <div><strong>مشتریان</strong><p>نوع محتوای مشتریان، دسته‌بندی اختصاصی، تصویر، توضیحات HTML، آدرس، دسته محصولات، ویدیو و گالری به همراه شورت‌کدها و Dynamic Tagهای المنتور اضافه می‌شود.</p></div>
                <div><strong>پروژه‌ها</strong><p>نوع محتوای مستقل پروژه‌ها، دسته‌بندی اختصاصی، پیمانکار، تصویر، توضیحات HTML، آدرس، دسته محصولات، ویدیو و گالری به همراه شورت‌کدها و Dynamic Tagهای المنتور اضافه می‌شود.</p></div>
            </div>
        </section>
        <?php self::moduleCards(['customers', 'projects']); ?>
        <?php
    }

    /** @param array<int, string> $keys */
    private static function moduleCards(array $keys): void
    {
        $modules = Application::instance()->get('modules');
        if (! $modules instanceof ModuleManager) {
            return;
        }

        $registry = $modules->registry();
        ?>
        <div class="fa-ajax-notice" role="status" aria-live="polite"></div>
        <section class="fa-modules-grid">
            <?php foreach ($keys as $key) :
                if (! isset($registry[$key])) {
                    continue;
                }
                $item = $registry[$key];
                $on = $modules->enabled($key);
                ?>
                <article class="fa-module-card">
                    <div class="fa-module-header"><span class="fa-module-icon"><span class="dashicons <?php echo esc_attr($item['icon'] ?? 'dashicons-admin-plugins'); ?>" aria-hidden="true"></span></span><div class="fa-module-title"><h2><?php echo esc_html($item['title'] ?? $key); ?></h2><small><?php echo esc_html($item['description'] ?? ''); ?></small></div></div>
                    <div class="fa-module-footer"><span class="fa-status"><?php echo $on ? 'فعال' : 'غیرفعال'; ?></span><label class="fa-toggle"><span class="screen-reader-text"><?php echo esc_html('تغییر وضعیت ' . ($item['title'] ?? $key)); ?></span><input class="fa-module-toggle" type="checkbox" data-module="<?php echo esc_attr($key); ?>" <?php checked($on); ?>><span class="fa-toggle-slider" aria-hidden="true"></span></label></div>
                </article>
            <?php endforeach; ?>
        </section>
        <?php
    }

    private static function theme(): void
    {
        $settings = (new ThemeManager())->settings();
        $presets = [
            'glass' => 'شیشه‌ای فندق',
            'midnight' => 'نیمه‌شب',
            'clean' => 'ساده و روشن',
        ];
        $colors = [
            'primary' => 'رنگ اصلی',
            'secondary' => 'رنگ مکمل',
            'background' => 'پس‌زمینه',
            'surface' => 'سطح کارت‌ها',
            'text' => 'متن اصلی',
            'muted' => 'متن کم‌رنگ',
            'border' => 'حاشیه',
            'success' => 'موفقیت',
            'warning' => 'هشدار',
            'danger' => 'خطا',
        ];
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">مدیریت پوسته پنل</h1><p>ظاهر پنل‌های فندق را تنظیم کنید؛ CSS فقط هنگام ذخیره دوباره تولید می‌شود.</p></div><span class="fa-admin-build">Static CSS Engine</span></header>
        <form id="fa-theme-form" class="fa-theme-layout">
            <section class="fa-panel fa-theme-controls">
                <header><span class="dashicons dashicons-art" aria-hidden="true"></span><h2>تنظیمات پوسته</h2></header>
                <label class="fa-theme-enabled"><input type="checkbox" name="settings[enabled]" value="1" <?php checked((bool) $settings['enabled']); ?>><span><strong>فعال‌بودن پوسته سفارشی</strong><small>در صورت غیرفعال‌سازی، استایل پیش‌فرض فندق بارگذاری می‌شود.</small></span></label>
                <div class="fa-theme-fields fa-theme-fields-main">
                    <label><span>قالب آماده</span><select name="settings[preset]" id="fa-theme-preset"><?php foreach ($presets as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($settings['preset'], $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
                    <label><span>حالت نمایش</span><select name="settings[scheme]"><option value="light" <?php selected($settings['scheme'], 'light'); ?>>روشن</option><option value="dark" <?php selected($settings['scheme'], 'dark'); ?>>تاریک</option><option value="system" <?php selected($settings['scheme'], 'system'); ?>>هماهنگ با سیستم</option></select></label>
                    <label><span>فونت</span><select name="settings[font]"><?php foreach (SettingsSchema::fonts() as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($settings['font'], $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
                </div>
                <h3>رنگ‌های روشن</h3>
                <div class="fa-theme-color-grid"><?php foreach ($colors as $key => $label) : ?><label><span><?php echo esc_html($label); ?></span><input type="color" name="settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) $settings[$key]); ?>"></label><?php endforeach; ?></div>
                <h3>رنگ‌های حالت تاریک</h3>
                <div class="fa-theme-color-grid"><?php foreach (['dark_background' => 'پس‌زمینه تاریک', 'dark_surface' => 'سطح تاریک', 'dark_text' => 'متن تاریک', 'dark_muted' => 'متن کم‌رنگ تاریک'] as $key => $label) : ?><label><span><?php echo esc_html($label); ?></span><input type="color" name="settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) $settings[$key]); ?>"></label><?php endforeach; ?></div>
                <div class="fa-theme-ranges">
                    <label><span>گردی گوشه‌ها: <output data-output="radius"><?php echo esc_html((string) $settings['radius']); ?></output>px</span><input type="range" name="settings[radius]" min="0" max="40" value="<?php echo esc_attr((string) $settings['radius']); ?>"></label>
                    <label><span>شدت محوشدگی: <output data-output="blur"><?php echo esc_html((string) $settings['blur']); ?></output>px</span><input type="range" name="settings[blur]" min="0" max="30" value="<?php echo esc_attr((string) $settings['blur']); ?>"></label>
                    <label><span>شفافیت شیشه: <output data-output="glass_opacity"><?php echo esc_html((string) $settings['glass_opacity']); ?></output>%</span><input type="range" name="settings[glass_opacity]" min="20" max="100" value="<?php echo esc_attr((string) $settings['glass_opacity']); ?>"></label>
                </div>
                <div class="fa-theme-actions"><button type="submit" class="button button-primary">ذخیره و تولید CSS</button><button type="button" class="button" id="fa-theme-reset">بازنشانی</button><button type="button" class="button" id="fa-theme-export">خروجی JSON</button><label class="button fa-theme-import">ورود JSON<input id="fa-theme-import" type="file" accept="application/json,.json"></label></div>
                <div class="fa-ajax-notice" role="status" aria-live="polite"></div>
            </section>
            <section class="fa-panel fa-theme-preview-panel">
                <header><span class="dashicons dashicons-visibility" aria-hidden="true"></span><h2>پیش‌نمایش زنده</h2></header>
                <div id="fa-theme-preview" class="fa-theme-preview">
                    <aside><div class="fa-preview-logo"><img src="<?php echo esc_url(FA_URL . 'assets/admin/images/logo.webp'); ?>" alt="فندق"></div><span class="is-active">پیشخوان</span><span>ماژول سئو</span><span>مدیریت CRM</span><span>تنظیمات</span></aside>
                    <main><div class="fa-preview-head"><strong>پیشخوان فندق</strong><small>مدیریت یکپارچه قابلیت‌ها</small></div><div class="fa-preview-stats"><span><b>۱۲</b><small>مشتریان</small></span><span><b>۸</b><small>پروژه‌ها</small></span><span><b>۲۴</b><small>نظرات</small></span></div><div class="fa-preview-card"><strong>سلامت سیستم</strong><p>همه سرویس‌های فندق فعال و آماده هستند.</p><button type="button">دکمه نمونه</button></div></main>
                </div>
                <p class="description">پیش‌نمایش فقط داخل این کادر تغییر می‌کند. فایل اصلی پس از زدن دکمه ذخیره ساخته خواهد شد.</p>
            </section>
        </form>
        <?php
    }

    private static function settings(): void
    {
        $deleteData = (bool) get_option(Options::DELETE_DATA_ON_UNINSTALL, false);
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">تنظیمات</h1><p>تنظیمات عمومی فریم‌ورک فندق</p></div></header>
        <section class="fa-panel"><header><span class="dashicons dashicons-database" aria-hidden="true"></span><h2>نگهداری داده‌ها</h2></header><form id="fa-settings-form" class="fa-settings-form"><label class="fa-danger-setting"><input type="checkbox" name="delete_data" value="1" <?php checked($deleteData); ?>><span><strong>حذف کامل داده‌ها هنگام پاک‌کردن افزونه</strong><small>با فعال‌کردن این گزینه، مشتریان، پروژه‌ها، نظرات، دسته‌بندی‌ها و متاهای فندق در Uninstall حذف می‌شوند. فایل‌های رسانه‌ای حذف نخواهند شد.</small></span></label><p><button type="submit" class="button button-primary">ذخیره تنظیمات</button></p><div class="fa-ajax-notice" role="status" aria-live="polite"></div></form></section>
        <?php
    }

    private static function support(): void
    {
        ?><header class="fa-admin-welcome"><div><h1 tabindex="-1">پشتیبانی</h1><p>راهنما و مسیرهای ارتباطی فندق</p></div></header><section class="fa-panel"><header><span class="dashicons dashicons-heart" aria-hidden="true"></span><h2>نیاز به راهنمایی دارید؟</h2></header><p>برای دریافت راهنمایی و گزارش مشکل با تیم پشتیبانی فندق در ارتباط باشید.</p><p><a class="button button-primary" href="https://fandoogh.ir" target="_blank" rel="noopener noreferrer">ورود به پشتیبانی</a></p></section><?php
    }
}
