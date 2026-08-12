<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

final class ProductAdmin
{
    private const NONCE_ACTION = 'fa_save_product_content';
    private const NONCE_FIELD = 'fa_product_content_nonce';

    public function __construct(
        private readonly bool $faqEnabled = true,
        private readonly bool $reasonEnabled = true
    ) {
    }

    public function boot(): void
    {
        add_action('add_meta_boxes_product', [$this, 'addMetaBoxes']);
        add_action('save_post_product', [$this, 'save'], 10, 3);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function addMetaBoxes(): void
    {
        if ($this->faqEnabled) {
            add_meta_box(
                'fa-product-faq',
                'سؤالات متداول محصول',
                [$this, 'renderFaq'],
                'product',
                'normal',
                'default'
            );
        }

        if ($this->reasonEnabled) {
            add_meta_box(
                'fa-product-reason',
                'پاسخ تک‌سؤالی محصول',
                [$this, 'renderReason'],
                'product',
                'normal',
                'default'
            );
        }
    }

    public function enqueueAssets(string $hookSuffix): void
    {
        $screen = get_current_screen();

        if (! $screen || $screen->id !== 'product' || ! in_array($hookSuffix, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $productId = isset($_GET['post']) ? absint($_GET['post']) : 0;

        wp_enqueue_editor();

        if ($this->faqEnabled) {
            wp_enqueue_script(
                'fa-product-content-admin',
                FA_URL . 'assets/admin/js/product-content.js',
                ['jquery'],
                FA_BUILD,
                true
            );
            wp_localize_script(
                'fa-product-content-admin',
                'faProductContentAdmin',
                [
                    'items' => $productId > 0 ? ProductService::faq($productId) : [],
                    'questionPlaceholder' => 'متن سؤال…',
                    'removeLabel' => 'حذف',
                ]
            );
        }
        wp_enqueue_style(
            'fa-product-content-admin',
            FA_URL . 'assets/admin/css/product-content.css',
            [],
            FA_BUILD
        );
    }

    public function renderFaq(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
        ?>
        <div class="fa-product-content-box">
            <p class="description">
                سؤال و پاسخ‌های مخصوص همین محصول را اضافه کنید. پاسخ‌ها از HTML امن پشتیبانی می‌کنند.
            </p>
            <div id="fa-product-faq-wrapper"></div>
            <input type="hidden" id="fa_product_faq" name="fa_product_faq" value="">
            <p>
                <button type="button" class="button button-primary" id="fa-add-product-faq">
                    افزودن سؤال
                </button>
            </p>
            <p class="fa-product-content-shortcode">
                شورت‌کد: <code>[fa_product_faq]</code>
            </p>
        </div>
        <?php
    }

    public function renderReason(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_FIELD);
        $reason = ProductService::reason($post->ID);
        $question = $reason['question'] !== ''
            ? $reason['question']
            : ProductService::DEFAULT_REASON_QUESTION;
        ?>
        <div class="fa-product-content-box">
            <p>
                <label for="fa_product_reason_question"><strong>عنوان سؤال</strong></label>
            </p>
            <input
                type="text"
                class="widefat"
                id="fa_product_reason_question"
                name="fa_product_reason_question"
                value="<?php echo esc_attr($question); ?>"
            >
            <p><strong>پاسخ</strong></p>
            <?php
            wp_editor(
                $reason['answer'],
                'fa_product_reason_answer_editor',
                [
                    'textarea_name' => 'fa_product_reason_answer',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                    'teeny' => false,
                    'quicktags' => true,
                ]
            );
            ?>
            <p class="description">
                این بخش برای توضیح روشن مزیت اصلی محصول است. فقط HTML مجاز وردپرس ذخیره می‌شود.
            </p>
            <p class="fa-product-content-shortcode">
                شورت‌کد: <code>[fa_product_reason]</code>
            </p>
        </div>
        <?php
    }

    public function save(int $postId, \WP_Post $post, bool $update): void
    {
        unset($update);

        if (
            wp_is_post_autosave($postId)
            || wp_is_post_revision($postId)
            || $post->post_type !== 'product'
            || ! current_user_can('edit_post', $postId)
            || ! isset($_POST[self::NONCE_FIELD])
            || ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST[self::NONCE_FIELD])),
                self::NONCE_ACTION
            )
        ) {
            return;
        }

        if ($this->faqEnabled && isset($_POST['fa_product_faq'])) {
            $items = json_decode(wp_unslash($_POST['fa_product_faq']), true);
            ProductService::saveFaq($postId, is_array($items) ? $items : []);
        }

        if (
            $this->reasonEnabled
            && isset($_POST['fa_product_reason_question'], $_POST['fa_product_reason_answer'])
        ) {
            ProductService::saveReason(
                $postId,
                (string) wp_unslash($_POST['fa_product_reason_question']),
                (string) wp_unslash($_POST['fa_product_reason_answer'])
            );
        }
    }
}
