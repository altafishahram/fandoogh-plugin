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
        wp_enqueue_style('fa-mega-menu-admin', FA_URL . 'modules/mega-menu/Assets/css/admin.css', [], FA_VERSION);
        wp_add_inline_script('jquery-core', 'jQuery(function($){$(".fa-mega-image-select").on("click",function(e){e.preventDefault();var input=$(this).siblings("input"),frame=wp.media({title:"انتخاب تصویر بنر",multiple:false});frame.on("select",function(){input.val(frame.state().get("selection").first().id);});frame.open();});$("input[name=\"settings[category_display_mode]\"]").on("change",function(){$(".fa-mega-category-list").toggleClass("is-disabled",this.value!=="selected");}).trigger("change");});');
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
        $selected = array_map('absint', (array) $settings['visible_category_ids']);
        ?>
        <div class="wrap fa-mega-settings">
            <header class="fa-mega-settings__header"><div><h1><?php esc_html_e('تنظیمات مگا منو', 'fandoogh'); ?></h1><p><?php esc_html_e('نمایش، رفتار و ظاهر منوی دسته‌بندی را از یک جا مدیریت کنید.', 'fandoogh'); ?></p></div><code>[fa_mega_menu]</code></header>
            <?php if (isset($_GET['updated'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('تنظیمات ذخیره شد.', 'fandoogh'); ?></p></div><?php endif; ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="fa_save_mega_menu">
                <?php wp_nonce_field('fa_save_mega_menu'); ?>
                <section class="fa-mega-settings-card"><h2><?php esc_html_e('عمومی و رفتار منو', 'fandoogh'); ?></h2><div class="fa-mega-fields fa-mega-fields--three">
                    <?php self::field('button_text', __('متن دکمه', 'fandoogh'), 'text', $settings['button_text']); ?>
                    <label><span><?php esc_html_e('آیکون دکمه', 'fandoogh'); ?></span><select name="settings[button_icon]"><?php foreach (Service::buttonIcons() as $icon => $label) : ?><option value="<?php echo esc_attr($icon); ?>" <?php selected($settings['button_icon'], $icon); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
                    <label><span><?php esc_html_e('نحوه باز شدن', 'fandoogh'); ?></span><select name="settings[interaction_mode]"><option value="hover" <?php selected($settings['interaction_mode'], 'hover'); ?>><?php esc_html_e('هاور', 'fandoogh'); ?></option><option value="click" <?php selected($settings['interaction_mode'], 'click'); ?>><?php esc_html_e('کلیک', 'fandoogh'); ?></option></select></label>
                </div></section>
                <section class="fa-mega-settings-card"><h2><?php esc_html_e('دسته‌های نمایشی', 'fandoogh'); ?></h2><p class="description"><?php esc_html_e('فقط دسته‌های اصلی در ستون کناری مگامنو نمایش داده می‌شوند؛ زیر‌دسته‌ها داخل پنل همان دسته قرار می‌گیرند.', 'fandoogh'); ?></p>
                    <div class="fa-mega-choice"><label><input type="radio" name="settings[category_display_mode]" value="all" <?php checked($settings['category_display_mode'], 'all'); ?>> <?php esc_html_e('نمایش همه دسته‌های اصلی', 'fandoogh'); ?></label><label><input type="radio" name="settings[category_display_mode]" value="selected" <?php checked($settings['category_display_mode'], 'selected'); ?>> <?php esc_html_e('انتخاب دستی چند دسته', 'fandoogh'); ?></label></div>
                    <div class="fa-mega-category-list"><?php foreach ($categories as $category) : ?><label><input type="checkbox" name="settings[visible_category_ids][]" value="<?php echo esc_attr((string) $category->term_id); ?>" <?php checked(in_array($category->term_id, $selected, true)); ?>><span><?php echo esc_html($category->name); ?></span></label><?php endforeach; ?></div>
                </section>
                <?php self::styleCard(__('استایل دکمه و پوسته دسکتاپ', 'fandoogh'), $settings['styles'], ['button_bg', 'button_color', 'button_hover', 'button_active', 'button_radius', 'button_padding', 'button_font_size', 'button_shadow', 'menu_width', 'sidebar_width', 'menu_radius', 'menu_bg', 'menu_shadow']); ?>
                <?php self::styleCard(__('سایدبار و کارت‌های دسکتاپ', 'fandoogh'), $settings['styles'], ['sidebar_bg', 'sidebar_color', 'sidebar_hover', 'sidebar_hover_bg', 'sidebar_active', 'sidebar_active_bg', 'sidebar_font_size', 'sidebar_padding', 'grid_columns', 'card_border', 'card_hover_border', 'card_radius', 'card_hover_shadow', 'card_icon_bg', 'card_title_size', 'card_count_size', 'image_size']); ?>
                <?php self::styleCard(__('استایل منوی موبایل و تبلت', 'fandoogh'), $settings['styles'], ['mobile_bg', 'mobile_text_color', 'mobile_border_color', 'mobile_item_bg', 'mobile_item_hover_bg', 'mobile_item_hover_color', 'mobile_item_active_bg', 'mobile_item_active_color', 'mobile_icon_color', 'mobile_image_size', 'mobile_image_radius', 'mobile_item_radius', 'mobile_item_padding', 'mobile_item_gap', 'mobile_font_size']); ?>
                <?php self::styleCard(__('بنرهای دسکتاپ', 'fandoogh'), $settings['styles'], ['banner_height', 'banner_radius', 'banner_background']); ?>
                <details class="fa-mega-settings-card"><summary><?php esc_html_e('بنر اختصاصی هر دسته', 'fandoogh'); ?></summary><p class="description"><?php esc_html_e('اختیاری است؛ برای هر دستهٔ اصلی یک بنر مستقل بسازید.', 'fandoogh'); ?></p><?php foreach ($categories as $term) self::bannerFields($term, $settings['banners'][$term->term_id] ?? []); ?></details>
                <?php submit_button(__('ذخیره تنظیمات', 'fandoogh'), 'primary large'); ?>
            </form>
        </div>
        <?php
    }

    private static function styleCard(string $title, array $styles, array $keys): void
    {
        $fields = self::styleFields();
        echo '<section class="fa-mega-settings-card"><h2>' . esc_html($title) . '</h2><div class="fa-mega-fields">';
        foreach ($keys as $key) { [$label, $type] = $fields[$key]; self::field('styles][' . $key, $label, $type, $styles[$key] ?? '', 'settings[styles][' . $key . ']'); }
        echo '</div></section>';
    }

    private static function field(string $id, string $label, string $type, mixed $value, string $name = ''): void
    {
        $name = $name ?: 'settings[' . $id . ']';
        printf('<label><span>%s</span><input id="fa-mega-%s" name="%s" type="%s" value="%s"></label>', esc_html($label), esc_attr(str_replace(['][', ']'], '-', $id)), esc_attr($name), esc_attr($type), esc_attr((string) $value));
    }

    /** @return array<string, array{string, string}> */
    private static function styleFields(): array
    {
        return [
            'button_bg' => ['رنگ پس‌زمینه دکمه', 'color'], 'button_color' => ['رنگ متن دکمه', 'color'], 'button_hover' => ['رنگ هاور دکمه', 'color'], 'button_active' => ['رنگ فعال دکمه', 'color'], 'button_radius' => ['شعاع گوشه دکمه (px)', 'number'], 'button_padding' => ['پدینگ دکمه (px)', 'number'], 'button_font_size' => ['اندازه فونت دکمه (px)', 'number'], 'button_shadow' => ['سایه دکمه', 'text'],
            'menu_width' => ['عرض مگا منو (px)', 'number'], 'sidebar_width' => ['عرض سایدبار (px)', 'number'], 'menu_radius' => ['شعاع مگا منو (px)', 'number'], 'menu_bg' => ['پس‌زمینه مگا منو', 'color'], 'menu_shadow' => ['سایه مگا منو', 'text'],
            'sidebar_bg' => ['پس‌زمینه سایدبار', 'color'], 'sidebar_color' => ['رنگ متن سایدبار', 'color'], 'sidebar_hover' => ['رنگ هاور سایدبار', 'color'], 'sidebar_hover_bg' => ['پس‌زمینه هاور سایدبار', 'color'], 'sidebar_active' => ['رنگ متن فعال سایدبار', 'color'], 'sidebar_active_bg' => ['پس‌زمینه فعال سایدبار', 'color'], 'sidebar_font_size' => ['اندازه فونت سایدبار (px)', 'number'], 'sidebar_padding' => ['پدینگ آیتم سایدبار (px)', 'number'],
            'grid_columns' => ['تعداد ستون گرید (۲ تا ۴)', 'number'], 'card_border' => ['رنگ حاشیه کارت', 'color'], 'card_hover_border' => ['حاشیه هاور کارت', 'color'], 'card_radius' => ['شعاع کارت (px)', 'number'], 'card_hover_shadow' => ['سایه هاور کارت', 'text'], 'card_icon_bg' => ['پس‌زمینه آیکون کارت', 'color'], 'card_title_size' => ['اندازه عنوان کارت (px)', 'number'], 'card_count_size' => ['اندازه تعداد محصول (px)', 'number'], 'image_size' => ['اندازه تصویر دسته (px)', 'number'],
            'banner_height' => ['ارتفاع بنر (px)', 'number'], 'banner_radius' => ['شعاع بنر (px)', 'number'], 'banner_background' => ['پس‌زمینه یا گرادیان بنر', 'text'],
            'mobile_bg' => ['پس‌زمینه منوی موبایل', 'color'], 'mobile_text_color' => ['رنگ متن', 'color'], 'mobile_border_color' => ['رنگ حاشیه', 'color'], 'mobile_item_bg' => ['پس‌زمینه آیتم', 'color'], 'mobile_item_hover_bg' => ['پس‌زمینه هاور', 'color'], 'mobile_item_hover_color' => ['رنگ متن هاور', 'color'], 'mobile_item_active_bg' => ['پس‌زمینه انتخاب‌شده', 'color'], 'mobile_item_active_color' => ['رنگ متن انتخاب‌شده', 'color'], 'mobile_icon_color' => ['رنگ آیکون و فلش', 'color'], 'mobile_image_size' => ['اندازه تصویر دسته (px)', 'number'], 'mobile_image_radius' => ['شعاع تصویر (px)', 'number'], 'mobile_item_radius' => ['شعاع آیتم (px)', 'number'], 'mobile_item_padding' => ['پدینگ آیتم (px)', 'number'], 'mobile_item_gap' => ['فاصله تصویر و متن (px)', 'number'], 'mobile_font_size' => ['اندازه فونت (px)', 'number'],
        ];
    }

    private static function bannerFields(\WP_Term $term, array $banner): void
    {
        $id = $term->term_id; $type = $banner['type'] ?? 'text';
        ?>
        <fieldset class="fa-mega-banner-fields"><legend><strong><?php echo esc_html($term->name); ?></strong></legend>
            <div class="fa-mega-fields fa-mega-fields--three"><label><span><?php esc_html_e('نوع بنر', 'fandoogh'); ?></span><select name="settings[banners][<?php echo esc_attr((string) $id); ?>][type]"><option value="text" <?php selected($type, 'text'); ?>><?php esc_html_e('متن دستی', 'fandoogh'); ?></option><option value="image" <?php selected($type, 'image'); ?>><?php esc_html_e('تصویر', 'fandoogh'); ?></option><option value="html" <?php selected($type, 'html'); ?>>HTML</option></select></label><label><span><?php esc_html_e('شناسه تصویر', 'fandoogh'); ?></span><input type="number" name="settings[banners][<?php echo esc_attr((string) $id); ?>][image_id]" value="<?php echo esc_attr((string) ($banner['image_id'] ?? 0)); ?>"><button class="button fa-mega-image-select"><?php esc_html_e('انتخاب تصویر', 'fandoogh'); ?></button></label><label><span><?php esc_html_e('عنوان', 'fandoogh'); ?></span><input name="settings[banners][<?php echo esc_attr((string) $id); ?>][title]" value="<?php echo esc_attr((string) ($banner['title'] ?? '')); ?>"></label></div>
            <div class="fa-mega-fields"><label><span><?php esc_html_e('متن', 'fandoogh'); ?></span><textarea rows="2" name="settings[banners][<?php echo esc_attr((string) $id); ?>][text]"><?php echo esc_textarea((string) ($banner['text'] ?? '')); ?></textarea></label><label><span><?php esc_html_e('HTML دلخواه', 'fandoogh'); ?></span><textarea rows="2" name="settings[banners][<?php echo esc_attr((string) $id); ?>][html]"><?php echo esc_textarea((string) ($banner['html'] ?? '')); ?></textarea></label><label><span><?php esc_html_e('متن دکمه / لینک', 'fandoogh'); ?></span><input name="settings[banners][<?php echo esc_attr((string) $id); ?>][button_text]" value="<?php echo esc_attr((string) ($banner['button_text'] ?? '')); ?>"><input type="url" name="settings[banners][<?php echo esc_attr((string) $id); ?>][button_url]" value="<?php echo esc_attr((string) ($banner['button_url'] ?? '')); ?>"></label></div>
        </fieldset>
        <?php
    }
}
