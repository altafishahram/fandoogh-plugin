<?php

declare(strict_types=1);

namespace Fandoogh\CRM\PartnerPricing;

defined('ABSPATH') || exit;

final class Admin
{
    public function boot(): void
    {
        add_action('woocommerce_product_options_pricing', [$this, 'productField']);
        add_action('woocommerce_admin_process_product_object', [$this, 'saveProduct']);
        add_action('woocommerce_variation_options_pricing', [$this, 'variationField'], 10, 3);
        add_action('woocommerce_admin_process_variation_object', [$this, 'saveVariation'], 10, 2);
        add_action('show_user_profile', [$this, 'userField']);
        add_action('edit_user_profile', [$this, 'userField']);
        add_action('personal_options_update', [$this, 'saveUser']);
        add_action('edit_user_profile_update', [$this, 'saveUser']);
        add_filter('manage_users_columns', [$this, 'userColumns']);
        add_filter('manage_users_custom_column', [$this, 'userColumn'], 10, 3);
        add_action('admin_enqueue_scripts', [$this, 'assets']);
    }

    public function assets(): void
    {
        if (get_current_screen()?->id === 'product') {
            wp_enqueue_script('fa-partner-price-admin', FA_URL . 'assets/admin/js/partner-price.js', [], FA_BUILD, true);
        }
    }

    public function productField(): void
    {
        global $product_object;
        if (! $product_object instanceof \WC_Product) {
            return;
        }
        wp_nonce_field('fa_partner_price_product', 'fa_partner_price_nonce');
        echo '<div class="show_if_simple">';
        woocommerce_wp_text_input([
            'id' => Pricing::PRICE_META,
            'label' => __('قیمت همکاری', 'fandoogh') . ' (' . get_woocommerce_currency_symbol() . ')',
            'data_type' => 'price',
            'value' => $product_object->get_meta(Pricing::PRICE_META, true, 'edit'),
            'description' => __('فقط برای همکاران واردشده؛ خالی = قیمت معمول. مبلغ را مانند قیمت معمول محصول و با همان واحد پول وارد کنید.', 'fandoogh'),
            'desc_tip' => true,
        ]);
        self::shortcodeHelp($product_object->get_id());
        echo '</div>';
    }

    public function variationField($loop, $variationData, $variation): void
    {
        $product = wc_get_product($variation->ID);
        if (! $product) {
            return;
        }
        wp_nonce_field('fa_partner_price_variation_' . $product->get_id(), 'fa_partner_variation_nonce[' . $loop . ']');
        woocommerce_wp_text_input([
            'id' => 'fa_partner_variation_price_' . $loop,
            'name' => 'fa_partner_variation_price[' . $loop . ']',
            'label' => __('قیمت همکاری این تنوع', 'fandoogh') . ' (' . get_woocommerce_currency_symbol() . ')',
            'data_type' => 'price',
            'value' => $product->get_meta(Pricing::PRICE_META, true, 'edit'),
            'wrapper_class' => 'form-row form-row-full',
            'description' => __('خالی = قیمت معمول همین تنوع؛ قیمت هر تنوع مستقل است.', 'fandoogh'),
            'desc_tip' => true,
        ]);
        self::shortcodeHelp($product->get_id());
    }

    private static function shortcodeHelp(int $id): void
    {
        $shortcode = '[fa_partner_price product_id="' . $id . '"]';
        echo '<p class="form-field form-row-full fa-partner-shortcode"><span>' . esc_html__('نمایش در هر برگه برای همکاران:', 'fandoogh') . '</span> <code dir="ltr">' . esc_html($shortcode) . '</code> <button type="button" class="button" data-fa-copy="' . esc_attr($shortcode) . '">' . esc_html__('کپی شورت‌کد', 'fandoogh') . '</button> <small role="status" aria-live="polite"></small><br>' . esc_html__('در قالب محصول از [fa_partner_price] استفاده کنید. show_cart="yes" دکمه خرید را اضافه می‌کند.', 'fandoogh') . '</p>';
    }

    public function saveProduct(\WC_Product $product): void
    {
        $nonce = $_POST['fa_partner_price_nonce'] ?? '';
        if (! is_string($nonce) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'fa_partner_price_product') || ! current_user_can('edit_post', $product->get_id()) || ! $product->is_type('simple')) {
            return;
        }
        if (array_key_exists(Pricing::PRICE_META, $_POST)) {
            self::saveAmount($product, wp_unslash($_POST[Pricing::PRICE_META]));
        }
    }

    public function saveVariation(\WC_Product $product, int $loop): void
    {
        $nonce = $_POST['fa_partner_variation_nonce'][$loop] ?? '';
        if (! is_string($nonce) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'fa_partner_price_variation_' . $product->get_id()) || ! current_user_can('edit_post', $product->get_id())) {
            return;
        }
        if (isset($_POST['fa_partner_variation_price']) && is_array($_POST['fa_partner_variation_price']) && array_key_exists($loop, $_POST['fa_partner_variation_price'])) {
            self::saveAmount($product, wp_unslash($_POST['fa_partner_variation_price'][$loop]));
        }
    }

    private static function saveAmount(\WC_Product $product, mixed $value): void
    {
        if (! is_scalar($value)) {
            return;
        }
        $value = trim((string) $value);
        if ($value === '') {
            $product->delete_meta_data(Pricing::PRICE_META);
            return;
        }
        $price = wc_format_decimal($value);
        if ($price === '' || ! is_numeric($price) || ! is_finite((float) $price) || (float) $price < 0) {
            \WC_Admin_Meta_Boxes::add_error(__('قیمت همکاری نامعتبر است؛ مقدار قبلی حفظ شد.', 'fandoogh'));
            return;
        }
        $product->update_meta_data(Pricing::PRICE_META, $price);
    }

    public function userField(\WP_User $user): void
    {
        if (! Access::canManage($user->ID)) {
            return;
        }
        wp_nonce_field('fa_partner_user_' . $user->ID, 'fa_partner_user_nonce');
        ?>
        <h2><?php esc_html_e('قیمت همکاری فندق', 'fandoogh'); ?></h2>
        <table class="form-table" role="presentation"><tr><th><?php esc_html_e('دسترسی همکار', 'fandoogh'); ?></th><td>
            <label><input type="checkbox" name="fa_is_partner" value="yes" <?php checked(get_user_meta($user->ID, Access::USER_META, true), 'yes'); ?>> <?php esc_html_e('این کاربر همکار تأییدشده است.', 'fandoogh'); ?></label>
            <p class="description"><?php esc_html_e('پس از ورود می‌تواند با قیمت همکاری خرید کند. برداشتن تیک، دسترسی را برای خریدهای بعدی و سبد جاری لغو می‌کند؛ سفارش‌های ثبت‌شده تغییر نمی‌کنند.', 'fandoogh'); ?></p>
        </td></tr></table>
        <?php
    }

    public function saveUser(int $userId): void
    {
        $nonce = $_POST['fa_partner_user_nonce'] ?? '';
        if (! Access::canManage($userId) || ! is_string($nonce) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), 'fa_partner_user_' . $userId)) {
            return;
        }
        if (($_POST['fa_is_partner'] ?? '') === 'yes') {
            update_user_meta($userId, Access::USER_META, 'yes');
        } else {
            delete_user_meta($userId, Access::USER_META);
        }
    }

    public function userColumns(array $columns): array
    {
        $columns['fa_partner'] = __('همکار فندق', 'fandoogh');
        return $columns;
    }

    public function userColumn($value, string $column, int $userId)
    {
        return $column === 'fa_partner'
            ? (get_user_meta($userId, Access::USER_META, true) === 'yes' ? esc_html__('همکار', 'fandoogh') : '—')
            : $value;
    }
}
