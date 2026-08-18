<?php

declare(strict_types=1);

namespace Fandoogh\Admin;

use Fandoogh\Core\Application;
use Fandoogh\Core\Constants\Options;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\AdminTheme\SettingsSchema;
use Fandoogh\AdminTheme\ThemeManager;
use Fandoogh\Modules\OrderCenter\Module as OrderCenterModule;

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
        } elseif ($section === 'order_center') {
            OrderCenterModule::render();
        } elseif ($section === 'calculator') {
            \Fandoogh\Calculator\AdminPage::render();
        } elseif ($section === 'crm') {
            self::crm();
        } elseif ($section === 'theme') {
            self::theme();
        } elseif ($section === 'settings') {
            self::settings();
        } elseif ($section === 'wp_login') {
            self::wpLogin();
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
                <div><strong>مرکز سفارشات</strong><p>پس از فعال‌سازی، مدیریت سفارش‌های ووکامرس، KPI، جست‌وجو، فیلتر، جزئیات سفارش، تغییر وضعیت و یادداشت داخلی به منوی اصلی وردپرس اضافه می‌شود.</p></div>
            </div>
        </section>
        <?php self::moduleCards(['customers', 'projects', 'order-center']); ?>
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
                $available = $key !== 'order-center' || OrderCenterModule::isAvailable();
                $status = $on && $available ? 'فعال' : 'غیرفعال';
                ?>
                <article class="fa-module-card">
                    <div class="fa-module-header"><span class="fa-module-icon"><span class="dashicons <?php echo esc_attr($item['icon'] ?? 'dashicons-admin-plugins'); ?>" aria-hidden="true"></span></span><div class="fa-module-title"><h2><?php echo esc_html($item['title'] ?? $key); ?></h2><small><?php echo esc_html($item['description'] ?? ''); ?></small></div></div>
                    <div class="fa-module-footer"><span class="fa-status"><?php echo esc_html($status); ?></span><label class="fa-toggle"><span class="screen-reader-text"><?php echo esc_html('تغییر وضعیت ' . ($item['title'] ?? $key)); ?></span><input class="fa-module-toggle" type="checkbox" data-module="<?php echo esc_attr($key); ?>" <?php checked($on); ?> <?php disabled(! $available && ! $on); ?>><span class="fa-toggle-slider" aria-hidden="true"></span></label></div>
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

    private static function wpLogin(): void
    {
        $o = \Fandoogh\LoginDesigner\Settings::get();
        ?>
        <header class="fa-admin-welcome"><div><h1 tabindex="-1">طراحی صفحه لاگین وردپرس</h1><p>تنظیمات زیر روی ظاهر صفحه ورود ادمین (wp-login.php) اعمال می‌شود.</p></div></header>
        <section class="fa-panel fa-panel-wide">
        <form method="post" action="options.php" class="fa-settings-form">
            <?php settings_fields('fa_login_settings_group'); ?>

            <h3>🌈 پس‌زمینه</h3>
            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px;">
                <button type="button" class="button fa-preset-btn" data-c1="#4a1d8f" data-c2="#2a4d8f" data-b1="#4a1d8f" data-b2="#2a4d8f">بنفش/آبی ملایم (پیش‌فرض)</button>
                <button type="button" class="button fa-preset-btn" data-c1="#1a1a2e" data-c2="#16213e" data-b1="#0f3460" data-b2="#16213e">شب تاریک شیک</button>
                <button type="button" class="button fa-preset-btn" data-c1="#3a1c71" data-c2="#6a0572" data-b1="#6a0572" data-b2="#3a1c71">بنفش سلطنتی</button>
                <button type="button" class="button fa-preset-btn" data-c1="#134e5e" data-c2="#71b280" data-b1="#134e5e" data-b2="#71b280">سبز زمردی آرام</button>
                <button type="button" class="button fa-preset-btn" data-c1="#3e5151" data-c2="#decba4" data-b1="#3e5151" data-b2="#decba4">کرم و طلایی ملایم</button>
                <button type="button" class="button fa-preset-btn" data-c1="#232526" data-c2="#414345" data-b1="#414345" data-b2="#232526">مینیمال طوسی</button>
            </div>

            <div class="fa-theme-color-grid">
                <label><span>رنگ اول گرادیان</span><input type="text" class="fa-color-field" name="fa_login_options[bg_color_1]" value="<?php echo esc_attr($o['bg_color_1']); ?>"></label>
                <label><span>رنگ دوم گرادیان</span><input type="text" class="fa-color-field" name="fa_login_options[bg_color_2]" value="<?php echo esc_attr($o['bg_color_2']); ?>"></label>
            </div>

            <label class="fa-danger-setting" style="border-color:#e3e8ee; background:#f6f7f7; margin-top:20px;">
                <input type="checkbox" name="fa_login_options[animate_bg]" value="1" <?php checked($o['animate_bg'], 1); ?>>
                <span><strong>انیمیشن پس‌زمینه</strong><small>فعال کردن حرکت گرادیان در پس زمینه لاگین</small></span>
            </label>
            <label class="fa-danger-setting" style="border-color:#e3e8ee; background:#f6f7f7; margin-top:10px;">
                <input type="checkbox" name="fa_login_options[floating_shapes]" value="1" <?php checked($o['floating_shapes'], 1); ?>>
                <span><strong>اشکال شناور تزئینی</strong><small>نمایش هاله‌های نوری شناور در پس‌زمینه به صورت متحرک</small></span>
            </label>

            <h3 style="margin-top:30px;">💎 کارت فرم (شیشه‌ای)</h3>
            <div class="fa-theme-color-grid">
                <label><span>رنگ پایه کارت فرم</span><input type="text" class="fa-color-field" name="fa_login_options[form_bg_color]" value="<?php echo esc_attr($o['form_bg_color']); ?>"></label>
            </div>
            <div class="fa-theme-ranges" style="margin-top:20px; display:grid; gap:15px;">
                <label><span>شفافیت کارت (۰ تا ۱): <output><?php echo esc_html((string) $o['form_bg_opacity']); ?></output></span><input type="number" step="0.01" min="0" max="1" name="fa_login_options[form_bg_opacity]" value="<?php echo esc_attr((string) $o['form_bg_opacity']); ?>"></label>
                <label><span>میزان بلور شیشه‌ای (px): <output><?php echo esc_html((string) $o['blur_amount']); ?></output></span><input type="number" min="0" max="40" name="fa_login_options[blur_amount]" value="<?php echo esc_attr((string) $o['blur_amount']); ?>"></label>
                <label><span>گردی گوشه‌ها (px): <output><?php echo esc_html((string) $o['border_radius']); ?></output></span><input type="number" min="0" max="60" name="fa_login_options[border_radius]" value="<?php echo esc_attr((string) $o['border_radius']); ?>"></label>
            </div>
            <label class="fa-danger-setting" style="border-color:#e3e8ee; background:#f6f7f7; margin-top:15px;">
                <input type="checkbox" name="fa_login_options[shadow_enabled]" value="1" <?php checked($o['shadow_enabled'], 1); ?>>
                <span><strong>سایه کارت فرم</strong><small>سایه‌ی المان‌های شیشه‌ای برای بُعد دادن به فرم فعال باشد</small></span>
            </label>

            <h3 style="margin-top:30px;">🎯 رنگ‌ها و دکمه ورود</h3>
            <div class="fa-theme-color-grid">
                <label><span>رنگ متن اصلی</span><input type="text" class="fa-color-field" name="fa_login_options[text_color]" value="<?php echo esc_attr($o['text_color']); ?>"></label>
                <label><span>رنگ لیبل فیلدها</span><input type="text" class="fa-color-field" name="fa_login_options[label_color]" value="<?php echo esc_attr($o['label_color']); ?>"></label>
                <label><span>رنگ اول دکمه</span><input type="text" class="fa-color-field" name="fa_login_options[button_color_1]" value="<?php echo esc_attr($o['button_color_1']); ?>"></label>
                <label><span>رنگ دوم دکمه</span><input type="text" class="fa-color-field" name="fa_login_options[button_color_2]" value="<?php echo esc_attr($o['button_color_2']); ?>"></label>
            </div>

            <h3 style="margin-top:30px;">🖼 لوگوی اختصاصی</h3>
            <div style="display:grid; gap:15px; margin-top:10px;">
                <label style="display:flex; flex-direction:column; gap:8px;">
                    <span>تصویر لوگو (URL)</span>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="text" id="fa_logo_image" name="fa_login_options[logo_image]" value="<?php echo esc_attr($o['logo_image']); ?>" style="flex:1; max-width:400px; direction:ltr;">
                        <button type="button" class="button" id="fa_upload_logo_btn">انتخاب تصویر</button>
                    </div>
                    <img id="fa_logo_preview" src="<?php echo esc_url($o['logo_image']); ?>" style="max-width:150px; background:#f0f1f2; padding:10px; border-radius:10px; margin-top:10px; <?php echo $o['logo_image'] ? '' : 'display:none;'; ?>">
                </label>
                <label style="display:flex; flex-direction:column; gap:8px;"><span>عرض لوگو (px)</span><input type="number" min="20" max="400" name="fa_login_options[logo_width]" value="<?php echo esc_attr((string) $o['logo_width']); ?>" style="max-width:120px;"></label>
                <label style="display:flex; flex-direction:column; gap:8px;"><span>لینک لوگو (در صورت کلیک)</span><input type="url" name="fa_login_options[logo_link]" value="<?php echo esc_attr($o['logo_link']); ?>" style="max-width:400px; direction:ltr;"></label>
            </div>

            <h3 style="margin-top:30px;">✍️ متن و فونت</h3>
            <div style="display:grid; gap:15px;">
                <label style="display:flex; flex-direction:column; gap:8px;"><span>متن خوش‌آمدگویی بالای فرم</span><input type="text" name="fa_login_options[welcome_text]" value="<?php echo esc_attr($o['welcome_text']); ?>" style="max-width:500px;"></label>
                <label style="display:flex; flex-direction:column; gap:8px;"><span>نوع فونت صفحه</span>
                    <select name="fa_login_options[font_choice]" style="max-width:250px;">
                        <option value="vazirmatn" <?php selected($o['font_choice'], 'vazirmatn'); ?>>وزیرمتن (فارسی زیبا)</option>
                        <option value="system" <?php selected($o['font_choice'], 'system'); ?>>فونت پیش‌فرض سیستم (سریع‌ترین)</option>
                    </select>
                </label>
            </div>

            <h3 style="margin-top:30px;">⚙️ امکانات اضافی</h3>
            <label class="fa-danger-setting" style="border-color:#e3e8ee; background:#f6f7f7; margin-top:10px;">
                <input type="checkbox" name="fa_login_options[hide_back_to_blog]" value="1" <?php checked($o['hide_back_to_blog'], 1); ?>>
                <span><strong>مخفی کردن «بازگشت به سایت»</strong></span>
            </label>
            <label class="fa-danger-setting" style="border-color:#e3e8ee; background:#f6f7f7; margin-top:10px;">
                <input type="checkbox" name="fa_login_options[hide_language_switcher]" value="1" <?php checked($o['hide_language_switcher'], 1); ?>>
                <span><strong>مخفی کردن انتخابگر تغییر زبان</strong></span>
            </label>

            <div style="margin-top:40px; display:flex; gap:10px; border-top:1px solid #e3e8ee; padding-top:20px;">
                <button type="submit" class="button button-primary">ذخیره تنظیمات لاگین</button>
                <a href="<?php echo esc_url(wp_login_url()); ?>" target="_blank" class="button button-secondary">👀 مشاهده صفحه ورود</a>
            </div>

            <div class="fa-ajax-notice" role="status" aria-live="polite"></div>
        </form>
        </section>

        <script>
        jQuery(document).ready(function($){
            if ($.fn.wpColorPicker) {
                $('.fa-color-field').wpColorPicker();
            }

            $('.fa-preset-btn').on('click', function(){
                var c1 = $(this).data('c1'), c2 = $(this).data('c2');
                var b1 = $(this).data('b1'), b2 = $(this).data('b2');
                $('input[name="fa_login_options[bg_color_1]"]').wpColorPicker('color', c1);
                $('input[name="fa_login_options[bg_color_2]"]').wpColorPicker('color', c2);
                $('input[name="fa_login_options[button_color_1]"]').wpColorPicker('color', b1);
                $('input[name="fa_login_options[button_color_2]"]').wpColorPicker('color', b2);
            });

            var mediaUploader;
            $('#fa_upload_logo_btn').on('click', function(e){
                e.preventDefault();
                if ( mediaUploader ) { mediaUploader.open(); return; }
                mediaUploader = wp.media({
                    title: 'انتخاب لوگو',
                    button: { text: 'استفاده از این تصویر' },
                    multiple: false
                });
                mediaUploader.on('select', function(){
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#fa_logo_image').val(attachment.url);
                    $('#fa_logo_preview').attr('src', attachment.url).show();
                });
                mediaUploader.open();
            });
        });
        </script>
        <?php
    }
}
