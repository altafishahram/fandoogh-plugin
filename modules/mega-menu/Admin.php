<?php

declare(strict_types=1);

namespace Fandoogh\Modules\MegaMenu;

defined('ABSPATH') || exit;

final class Admin
{
    public function boot(): void
    {
        add_action('admin_post_fa_save_mega_menu', [$this, 'save']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'fandoogh_page_fa-mega-menu') return;
        wp_enqueue_media();
        wp_add_inline_script('jquery-core', 'jQuery(function($){$(".fa-mega-image-select").on("click",function(e){e.preventDefault();var input=$(this).siblings("input"),frame=wp.media({title:"انتخاب تصویر بنر",multiple:false});frame.on("select",function(){input.val(frame.state().get("selection").first().id);});frame.open();});});');
    }

    public function save(): void
    {
        if (! current_user_can('manage_options')) wp_die(esc_html__('دسترسی غیرمجاز است.', 'fandoogh'));
        check_admin_referer('fa_save_mega_menu');
        $input = isset($_POST['settings']) && is_array($_POST['settings']) ? wp_unslash($_POST['settings']) : [];
        Repository::save(Service::sanitize($input));
        wp_safe_redirect(add_query_arg(['page' => 'fa-mega-menu', 'updated' => '1'], admin_url('admin.php')));
        exit;
    }

    public static function renderPage(): void
    {
        if (! current_user_can('manage_options')) return;
        $settings = Service::settings();
        $categories = Repository::parentCategories(false);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('تنظیمات مگا منو', 'fandoogh'); ?></h1>
            <p><?php esc_html_e('شورت‌کد [fa_mega_menu] را در برگه، ابزارک یا قالب خود قرار دهید. تصویر دسته‌ها فقط از تصویر دسته‌بندی ووکامرس خوانده می‌شود.', 'fandoogh'); ?></p>
            <?php if (isset($_GET['updated'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('تنظیمات ذخیره شد.', 'fandoogh'); ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="fa_save_mega_menu">
                <?php wp_nonce_field('fa_save_mega_menu'); ?>
                <table class="form-table" role="presentation"><tbody>
                    <tr><th><label for="fa-mega-button-text"><?php esc_html_e('متن دکمه', 'fandoogh'); ?></label></th><td><input id="fa-mega-button-text" class="regular-text" name="settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>"></td></tr>
                    <tr><th><label for="fa-mega-button-icon"><?php esc_html_e('آیکون دکمه', 'fandoogh'); ?></label></th><td><select id="fa-mega-button-icon" name="settings[button_icon]"><?php foreach (Service::buttonIcons() as $icon => $label) : ?><option value="<?php echo esc_attr($icon); ?>" <?php selected($settings['button_icon'], $icon); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></td></tr>
                    <tr><th><label for="fa-mega-interaction-mode"><?php esc_html_e('نحوه بازشدن منو', 'fandoogh'); ?></label></th><td><select id="fa-mega-interaction-mode" name="settings[interaction_mode]"><option value="hover" <?php selected($settings['interaction_mode'], 'hover'); ?>><?php esc_html_e('هاور', 'fandoogh'); ?></option><option value="click" <?php selected($settings['interaction_mode'], 'click'); ?>><?php esc_html_e('کلیک', 'fandoogh'); ?></option></select></td></tr>
                </tbody></table>
                <h2><?php esc_html_e('ظاهر', 'fandoogh'); ?></h2>
                <table class="form-table" role="presentation"><tbody><?php self::styleFields($settings['styles']); ?></tbody></table>
                <h2><?php esc_html_e('بنر دسته‌ها', 'fandoogh'); ?></h2>
                <p><?php esc_html_e('برای هر دسته اصلی، یک بنر مستقل می‌توانید تنظیم کنید.', 'fandoogh'); ?></p>
                <?php foreach ($categories as $term) self::bannerFields($term, $settings['banners'][$term->term_id] ?? []); ?>
                <?php submit_button(__('ذخیره تنظیمات', 'fandoogh')); ?>
            </form>
        </div>
        <?php
    }

    private static function styleFields(array $styles): void
    {
        $fields = [
            'button_bg' => ['رنگ پس‌زمینه دکمه', 'color'], 'button_color' => ['رنگ متن دکمه', 'color'], 'button_hover' => ['رنگ هاور دکمه', 'color'], 'button_active' => ['رنگ فعال دکمه', 'color'], 'button_radius' => ['شعاع گوشه دکمه (px)', 'number'], 'button_padding' => ['پدینگ دکمه (px)', 'number'], 'button_font_size' => ['اندازه فونت دکمه (px)', 'number'], 'button_shadow' => ['سایه دکمه', 'text'],
            'menu_width' => ['عرض مگا منو (px)', 'number'], 'sidebar_width' => ['عرض سایدبار (px)', 'number'], 'menu_radius' => ['شعاع مگا منو (px)', 'number'], 'menu_bg' => ['پس‌زمینه مگا منو', 'color'], 'menu_shadow' => ['سایه مگا منو', 'text'],
            'sidebar_bg' => ['پس‌زمینه سایدبار', 'color'], 'sidebar_color' => ['رنگ متن سایدبار', 'color'], 'sidebar_hover' => ['رنگ هاور سایدبار', 'color'], 'sidebar_hover_bg' => ['پس‌زمینه هاور سایدبار', 'color'], 'sidebar_active' => ['رنگ متن حالت فعال سایدبار', 'color'], 'sidebar_active_bg' => ['پس‌زمینه حالت فعال سایدبار', 'color'], 'sidebar_font_size' => ['اندازه فونت سایدبار (px)', 'number'], 'sidebar_padding' => ['پدینگ آیتم سایدبار (px)', 'number'],
            'grid_columns' => ['تعداد ستون‌های گرید (۲ تا ۴)', 'number'], 'card_border' => ['رنگ حاشیه کارت', 'color'], 'card_hover_border' => ['رنگ حاشیه هاور کارت', 'color'], 'card_radius' => ['شعاع کارت (px)', 'number'], 'card_hover_shadow' => ['سایه هاور کارت', 'text'], 'card_icon_bg' => ['پس‌زمینه آیکون کارت', 'color'], 'card_title_size' => ['اندازه عنوان کارت (px)', 'number'], 'card_count_size' => ['اندازه تعداد محصول (px)', 'number'], 'image_size' => ['اندازه تصویر دسته (px)', 'number'],
            'banner_height' => ['ارتفاع بنر (px)', 'number'], 'banner_radius' => ['شعاع بنر (px)', 'number'], 'banner_background' => ['پس‌زمینه/گرادیان بنر', 'text'],
        ];
        foreach ($fields as $key => [$label, $type]) echo '<tr><th><label for="fa-mega-' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td><input id="fa-mega-' . esc_attr($key) . '" name="settings[styles][' . esc_attr($key) . ']" type="' . esc_attr($type) . '" value="' . esc_attr((string) ($styles[$key] ?? '')) . '" class="' . ($type === 'text' ? 'regular-text' : '') . '"></td></tr>';
    }

    private static function bannerFields(\WP_Term $term, array $banner): void
    {
        $id = $term->term_id; $type = $banner['type'] ?? 'text';
        ?>
        <fieldset style="border:1px solid #ccd0d4;padding:16px;margin:16px 0"><legend><strong><?php echo esc_html($term->name); ?></strong></legend>
            <p><label><?php esc_html_e('نوع بنر', 'fandoogh'); ?> <select name="settings[banners][<?php echo esc_attr((string) $id); ?>][type]"><option value="text" <?php selected($type, 'text'); ?>><?php esc_html_e('متن دستی', 'fandoogh'); ?></option><option value="image" <?php selected($type, 'image'); ?>><?php esc_html_e('تصویر', 'fandoogh'); ?></option><option value="html" <?php selected($type, 'html'); ?>>HTML</option></select></label></p>
            <p><label><?php esc_html_e('شناسه تصویر', 'fandoogh'); ?> <input type="number" name="settings[banners][<?php echo esc_attr((string) $id); ?>][image_id]" value="<?php echo esc_attr((string) ($banner['image_id'] ?? 0)); ?>"> <button class="button fa-mega-image-select"><?php esc_html_e('انتخاب تصویر', 'fandoogh'); ?></button></label></p>
            <p><label><?php esc_html_e('HTML دلخواه', 'fandoogh'); ?><br><textarea class="large-text" rows="4" name="settings[banners][<?php echo esc_attr((string) $id); ?>][html]"><?php echo esc_textarea((string) ($banner['html'] ?? '')); ?></textarea></label></p>
            <p><label><?php esc_html_e('عنوان', 'fandoogh'); ?> <input class="regular-text" name="settings[banners][<?php echo esc_attr((string) $id); ?>][title]" value="<?php echo esc_attr((string) ($banner['title'] ?? '')); ?>"></label></p>
            <p><label><?php esc_html_e('متن', 'fandoogh'); ?><br><textarea class="large-text" rows="2" name="settings[banners][<?php echo esc_attr((string) $id); ?>][text]"><?php echo esc_textarea((string) ($banner['text'] ?? '')); ?></textarea></label></p>
            <p><label><?php esc_html_e('متن دکمه', 'fandoogh'); ?> <input name="settings[banners][<?php echo esc_attr((string) $id); ?>][button_text]" value="<?php echo esc_attr((string) ($banner['button_text'] ?? '')); ?>"></label> <label><?php esc_html_e('لینک دکمه', 'fandoogh'); ?> <input type="url" class="regular-text" name="settings[banners][<?php echo esc_attr((string) $id); ?>][button_url]" value="<?php echo esc_attr((string) ($banner['button_url'] ?? '')); ?>"></label></p>
        </fieldset>
        <?php
    }
}
