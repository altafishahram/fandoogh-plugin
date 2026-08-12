<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

use Fandoogh\Core\Constants\Assets;
use Fandoogh\Core\Constants\Shortcodes as ShortcodeNames;

defined('ABSPATH') || exit;

final class Shortcode
{
    private static bool $localized = false;

    public function boot(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_action('wp_enqueue_scripts', [$this, 'maybeEnqueueAssets'], 20);
        add_shortcode(ShortcodeNames::FANDOOGH_CALCULATOR, [$this, 'render']);
    }

    public function registerAssets(): void
    {
        wp_register_style(
            Assets::FANDOOGH_CALCULATOR,
            FA_URL . Assets::FANDOOGH_CALCULATOR_CSS,
            [],
            FA_BUILD
        );
        wp_register_script(
            Assets::FANDOOGH_CALCULATOR,
            FA_URL . Assets::FANDOOGH_CALCULATOR_JS,
            [],
            FA_BUILD,
            true
        );
    }

    public function maybeEnqueueAssets(): void
    {
        $post = get_queried_object();
        if (! $post instanceof \WP_Post) {
            return;
        }

        $elementorData = (string) get_post_meta($post->ID, '_elementor_data', true);
        if (
            has_shortcode($post->post_content, ShortcodeNames::FANDOOGH_CALCULATOR)
            || str_contains($elementorData, ShortcodeNames::FANDOOGH_CALCULATOR)
        ) {
            $this->enqueueAssets();
        }
    }

    public function render(array $attributes = []): string
    {
        unset($attributes);
        $this->enqueueAssets();
        $products = Catalog::products();
        $id = wp_unique_id('fa-fandoogh-calculator-');
        $settings = SettingsService::get();
        
        // CSS Variables for styling
        $opacity = $settings['opacity'] / 100;
        $primary = $settings['color_primary'];
        $bg = $settings['color_bg'];
        $text = $settings['color_text'];

        ob_start();
        ?>
        <section class="fa-fandoogh-calculator" id="<?php echo esc_attr($id); ?>" dir="rtl" style="--fa-calc-opacity: <?php echo esc_attr((string)$opacity); ?>; --fa-calc-primary: <?php echo esc_attr($primary); ?>; --fa-calc-bg: <?php echo esc_attr($bg); ?>; --fa-calc-text: <?php echo esc_attr($text); ?>;">
            <div class="fa-fandoogh-calculator__glow" aria-hidden="true"></div>

            <?php if ($products === []) : ?>
                <p class="fa-fandoogh-calculator__empty">هنوز محصولی به ماشین حساب اختصاص داده نشده است.</p>
            <?php else : ?>
                <div class="fa-fandoogh-calculator__grid">
                    <div class="fa-fandoogh-calculator__fields">
                        <label class="fa-fandoogh-field">
                            <span>محصول</span>
                            <select class="fa-fandoogh-product">
                                <option value="">انتخاب محصول…</option>
                                <?php foreach ($products as $product) : ?>
                                    <option value="<?php echo esc_attr((string) $product->get_id()); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="fa-fandoogh-variations" aria-live="polite"></div>
                        <label class="fa-fandoogh-field">
                            <span><?php echo esc_html($settings['label_quantity']); ?></span>
                            <input class="fa-fandoogh-meters" type="number" min="1" step="1" inputmode="numeric" placeholder="مثلاً ۲۵">
                        </label>
                        <div class="fa-fandoogh-fees" aria-live="polite"></div>
                        <div class="fa-fandoogh-optional-fees" aria-live="polite"></div>
                    </div>

                    <aside class="fa-fandoogh-summary" aria-live="polite">
                        <span class="fa-fandoogh-summary__eyebrow">پیش‌فاکتور شما</span>
                        <div><small>قیمت هر <?php echo esc_html($settings['label_unit']); ?></small><strong class="fa-fandoogh-per-meter">—</strong></div>
                        <div class="fa-fandoogh-summary__total"><small>هزینه کل پروژه</small><strong class="fa-fandoogh-total">—</strong></div>
                        <button class="fa-fandoogh-order" type="button" disabled><?php echo esc_html($settings['label_submit']); ?></button>
                        <p class="fa-fandoogh-status" role="status"></p>
                    </aside>
                </div>
            <?php endif; ?>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    private function enqueueAssets(): void
    {
        if (! wp_style_is(Assets::FANDOOGH_CALCULATOR, 'registered')) {
            $this->registerAssets();
        }

        wp_enqueue_style(Assets::FANDOOGH_CALCULATOR);
        wp_enqueue_script(Assets::FANDOOGH_CALCULATOR);

        // Shortcodes rendered after wp_head still need their stylesheet printed.
        if (did_action('wp_print_styles')) {
            wp_print_styles(Assets::FANDOOGH_CALCULATOR);
        }

        if (! self::$localized) {
            $settings = SettingsService::get();
            wp_localize_script(Assets::FANDOOGH_CALCULATOR, 'faFandooghCalculator', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('fa_fandoogh_calculator'),
                'productAction' => 'get_product_variations_and_fixed_prices',
                'cartAction' => 'fandoogh_calculator_add_to_cart',
                'ctaAction' => $settings['cta_action'],
                'ctaTarget' => $settings['cta_target'],
                'unitLabel' => $settings['label_unit'],
                'labelMandatoryFees' => $settings['label_mandatory_fees'],
            ]);
            self::$localized = true;
        }
    }
}
