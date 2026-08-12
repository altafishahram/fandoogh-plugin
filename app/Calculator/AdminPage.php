<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class AdminPage
{
    public static function render(): void
    {
        $items = FixedPriceService::all();
        $settings = SettingsService::get();
        ?>
        <header class="fa-admin-welcome">
            <div>
                <h1 tabindex="-1">ماشین حساب فندق</h1>
                <p>تنظیمات جامع، قیمت‌های ثابت و شخصی‌سازی ماژول ماشین حساب. برای محصولات متغیر.</p>
            </div>
            <span class="fa-admin-build"><code>[fandoogh_calculator]</code></span>
        </header>

        <style>
            .fa-calculator-admin-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                padding-bottom: 24px;
            }
            /* سه باکس اول در یک ردیف */
            .fa-calculator-admin-grid > section:nth-of-type(-n+3) {
                flex: 1 1 calc((100% - 32px) / 3) !important;
                box-sizing: border-box;
                min-width: 0; /* جلوگیری از بیرون زدن محتوا */
            }
            /* باکس چهارم زیر سه تا باکس اول با عرض کامل */
            .fa-calculator-admin-grid > section:nth-of-type(4) {
                flex: 1 1 100% !important;
                box-sizing: border-box;
            }
            .fa-calculator-admin-actions-wrap {
                flex: 1 1 100%;
                display: flex;
                justify-content: flex-end; /* در RTL انتهای کادر سمت چپ می‌شود */
                align-items: center;
                gap: 16px;
                margin-top: 16px;
                padding: 16px;
            }
            .fa-calculator-admin-actions-wrap .button-primary {
                border-radius: 12px; /* متناسب با باکس‌ها */
            }
            .fa-calculator-admin-panel .inner-grid {
                display: grid;
                gap: 16px;
                padding: 16px;
                box-sizing: border-box;
            }
        </style>

        <form id="fa-fixed-prices-form" class="fa-calculator-admin-form">
            <div class="fa-calculator-admin-grid">
            
                <!-- 1. CTA -->
                <section class="fa-panel fa-calculator-admin-panel">
                    <header>
                        <span class="dashicons dashicons-external" aria-hidden="true"></span>
                        <div>
                            <h2>عملیات دکمه (CTA)</h2>
                            <p>تعیین کنید دکمه ثبت چه اقدامی انجام دهد.</p>
                        </div>
                    </header>
                    <div class="inner-grid">
                        <label>
                            <strong>نوع عملیات</strong><br>
                            <select name="settings[cta_action]" style="width: 100%; max-width: none;">
                                <option value="woocommerce_cart" <?php selected($settings['cta_action'], 'woocommerce_cart'); ?>>افزودن به سبد خرید ووکامرس</option>
                                <option value="contact_direct" <?php selected($settings['cta_action'], 'contact_direct'); ?>>هدایت به پیام‌رسان/لینک (واتساپ، تلگرام، بله و...)</option>
                                <option value="scroll_to_form" <?php selected($settings['cta_action'], 'scroll_to_form'); ?>>اسکرول نرم به فرم تماس (ID فرم)</option>
                            </select>
                        </label>
                        <label>
                            <strong>مقدار هدف (شماره / لینک / ID)</strong><br>
                            <input type="text" name="settings[cta_target]" value="<?php echo esc_attr($settings['cta_target']); ?>" placeholder="مثلاً 0912... یا https://t.me/id?text={message}" style="width: 100%; max-width: none;">
                            <p class="description">برای پیام‌رسان‌ها می‌توانید لینک کامل را قرار دهید و برای ارسال خودکار پیش‌فاکتور از <code>{message}</code> استفاده کنید.</p>
                        </label>
                        <label>
                            <strong>قالب متن پیش‌فاکتور (برای شبکه‌های اجتماعی)</strong><br>
                            <textarea name="settings[message_template]" rows="4" style="width: 100%; max-width: none;"><?php echo esc_textarea($settings['message_template'] ?? ''); ?></textarea>
                            <p class="description">متغیرهای مجاز: <code>{product}</code> (نام محصول)، <code>{quantity}</code> (مقدار/متراژ)، <code>{unit}</code> (واحد)، <code>{total}</code> (مبلغ کل)</p>
                        </label>
                    </div>
                </section>

                <!-- 2. Texts and General -->
                <section class="fa-panel fa-calculator-admin-panel">
                    <header>
                        <span class="dashicons dashicons-translation" aria-hidden="true"></span>
                        <div>
                            <h2>تنظیمات عمومی و متن‌ها</h2>
                            <p>دسته‌بندی‌های مجاز و شخصی‌سازی عناوین ماشین حساب.</p>
                        </div>
                    </header>
                    <div class="inner-grid">
                        <label>
                            <strong>دسته‌بندی‌های مجاز برای ماشین حساب</strong><br>
                            <select multiple name="settings[allowed_categories][]" class="wc-enhanced-select" style="width: 100%;" data-placeholder="انتخاب دسته‌بندی‌ها (خالی = همه)">
                                <?php
                                $categories = get_terms([
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => false,
                                ]);
                                $allowedCats = $settings['allowed_categories'] ?? [];
                                foreach ($categories as $cat) {
                                    $selected = in_array($cat->term_id, $allowedCats, true) ? 'selected' : '';
                                    echo '<option value="' . esc_attr((string)$cat->term_id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
                                }
                                ?>
                            </select>
                        </label>
                        <label>
                            <strong>لیبل هزینه‌های لحاظ‌شده</strong><br>
                            <input type="text" name="settings[label_mandatory_fees]" value="<?php echo esc_attr($settings['label_mandatory_fees'] ?? 'هزینه‌های لحاظ‌شده'); ?>" style="width: 100%; max-width: none;">
                        </label>
                        <label>
                            <strong>لیبل مقدار/طول</strong><br>
                            <input type="text" name="settings[label_quantity]" value="<?php echo esc_attr($settings['label_quantity']); ?>" style="width: 100%; max-width: none;">
                        </label>
                        <label>
                            <strong>واحد اندازه‌گیری</strong><br>
                            <input type="text" name="settings[label_unit]" value="<?php echo esc_attr($settings['label_unit']); ?>" style="width: 100%; max-width: none;">
                        </label>
                        <label>
                            <strong>متن دکمه ثبت</strong><br>
                            <input type="text" name="settings[label_submit]" value="<?php echo esc_attr($settings['label_submit']); ?>" style="width: 100%; max-width: none;">
                        </label>
                    </div>
                </section>

                <!-- 3. Glassmorphism -->
                <section class="fa-panel fa-calculator-admin-panel">
                    <header>
                        <span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span>
                        <div>
                            <h2>تنظیمات ظاهر (Glassmorphism)</h2>
                            <p>شفافیت پس‌زمینه و رنگ‌بندی کارت ماشین حساب.</p>
                        </div>
                    </header>
                    <div class="inner-grid">
                        <label>
                            <strong>شفافیت پس‌زمینه (٪)</strong><br>
                            <input type="range" name="settings[opacity]" id="fa-opacity-slider" min="0" max="100" value="<?php echo esc_attr((string) $settings['opacity']); ?>" style="width: 100%;">
                            <span id="fa-opacity-val"><?php echo esc_html((string) $settings['opacity']); ?>%</span>
                        </label>
                        <label>
                            <strong>رنگ اصلی / آکسان</strong><br>
                            <input type="color" name="settings[color_primary]" value="<?php echo esc_attr($settings['color_primary']); ?>" style="height: 32px; width: 100%;">
                        </label>
                        <label>
                            <strong>رنگ پایه پس‌زمینه</strong><br>
                            <input type="color" name="settings[color_bg]" value="<?php echo esc_attr($settings['color_bg']); ?>" style="height: 32px; width: 100%;">
                        </label>
                        <label>
                            <strong>رنگ متن</strong><br>
                            <input type="color" name="settings[color_text]" value="<?php echo esc_attr($settings['color_text']); ?>" style="height: 32px; width: 100%;">
                        </label>
                    </div>
                </section>

                <!-- 4. Fixed Prices -->
                <section class="fa-panel fa-calculator-admin-panel">
                    <header>
                        <span class="dashicons dashicons-calculator" aria-hidden="true"></span>
                        <div>
                            <h2>مدیریت قیمت‌های ثابت</h2>
                            <p>مبالغ به تومان است. حالت اجباری مستقیماً به جمع کل اضافه می‌شود؛ حالت اختیاری به عنوان چک‌باکس نمایش داده می‌شود.</p>
                        </div>
                    </header>

                    <div id="fa-fixed-prices-list" class="fa-fixed-prices-list">
                        <?php foreach ($items as $index => $item) : ?>
                            <?php self::row($item, (string) $index); ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="fa-calculator-admin-actions" style="margin-top: 16px;">
                        <button type="button" class="button" id="fa-add-fixed-price">افزودن قیمت ثابت جدید</button>
                    </div>
                </section>
                
                <!-- Save Button (Bottom Left) -->
                <div class="fa-calculator-admin-actions-wrap">
                    <span class="fa-ajax-notice" role="status" aria-live="polite" style="margin: 0;"></span>
                    <button type="submit" class="button button-primary button-hero">ذخیره کلیه تنظیمات</button>
                </div>
            </div>
        </form>

        <template id="fa-fixed-price-template">
            <?php self::row([], '__INDEX__'); ?>
        </template>
        <?php
    }

    private static function row(array $item, string $index): void
    {
        $item = array_merge([
            'id' => '',
            'title' => '',
            'price' => '',
            'type' => FixedPriceService::PER_METER,
            'mode' => 'mandatory',
            'product_ids' => [],
            'enabled' => true,
        ], $item);
        $prefix = 'fixed_prices[' . $index . ']';
        ?>
        <article class="fa-fixed-price-row" data-fixed-price-row>
            <input type="hidden" data-fixed-price-id name="<?php echo esc_attr($prefix . '[id]'); ?>" value="<?php echo esc_attr((string) $item['id']); ?>">
            <div class="fa-fixed-price-row__head">
                <strong>ردیف هزینه</strong>
                <button type="button" class="button-link-delete fa-remove-fixed-price">حذف</button>
            </div>
            <div class="fa-fixed-price-fields">
                <label>
                    <span>عنوان قیمت ثابت</span>
                    <input type="text" name="<?php echo esc_attr($prefix . '[title]'); ?>" value="<?php echo esc_attr((string) $item['title']); ?>" placeholder="مثلاً جوش CO2" maxlength="150">
                </label>
                <label>
                    <span>مبلغ (تومان)</span>
                    <input type="number" name="<?php echo esc_attr($prefix . '[price]'); ?>" value="<?php echo esc_attr((string) $item['price']); ?>" min="0" step="1" inputmode="numeric">
                </label>
                <label>
                    <span>نوع محاسبه</span>
                    <select name="<?php echo esc_attr($prefix . '[type]'); ?>">
                        <option value="per_meter" <?php selected($item['type'], FixedPriceService::PER_METER); ?>>به ازای هر واحد</option>
                        <option value="lump_sum" <?php selected($item['type'], FixedPriceService::LUMP_SUM); ?>>هزینه مقطوع</option>
                    </select>
                </label>
                <label>
                    <span>حالت اعمال (اجباری/اختیاری)</span>
                    <select name="<?php echo esc_attr($prefix . '[mode]'); ?>">
                        <option value="mandatory" <?php selected($item['mode'], 'mandatory'); ?>>اجباری / روی محصول</option>
                        <option value="optional" <?php selected($item['mode'], 'optional'); ?>>اختیاری / افزودنی توسط کاربر</option>
                    </select>
                </label>
                <label class="fa-fixed-price-products">
                    <span>محصولات مرتبط</span>
                    <select
                        class="wc-product-search"
                        multiple="multiple"
                        name="<?php echo esc_attr($prefix . '[product_ids][]'); ?>"
                        data-placeholder="جست‌وجوی محصول…"
                        data-action="woocommerce_json_search_products"
                        data-allow_clear="true"
                    >
                        <?php foreach ((array) $item['product_ids'] as $productId) :
                            $product = wc_get_product((int) $productId);
                            if (! $product instanceof \WC_Product) {
                                continue;
                            }
                            ?>
                            <option value="<?php echo esc_attr((string) $product->get_id()); ?>" selected>
                                <?php echo esc_html($product->get_formatted_name()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="fa-fixed-price-status">
                    <span>وضعیت</span>
                    <input type="hidden" name="<?php echo esc_attr($prefix . '[enabled]'); ?>" value="0">
                    <span class="fa-toggle">
                        <input type="checkbox" name="<?php echo esc_attr($prefix . '[enabled]'); ?>" value="1" <?php checked((bool) $item['enabled']); ?>>
                        <span class="fa-toggle-slider" aria-hidden="true"></span>
                    </span>
                </label>
            </div>
        </article>
        <?php
    }
}
