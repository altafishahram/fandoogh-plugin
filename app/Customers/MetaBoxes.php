<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class MetaBoxes
{
    public function boot(): void
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_fa_customer', [$this, 'save']);
    }

    public function register(): void
    {
        add_meta_box(
            'fa_customer_information',
            __('اطلاعات مشتری', 'fandoogh'),
            [$this, 'render'],
            'fa_customer',
            'normal',
            'high'
        );
    }

    public function render(\WP_Post $post): void
    {
        wp_nonce_field('fa_customer_nonce', 'fa_customer_nonce');

        $data = CustomerManager::all($post->ID);
        $gallery = (array) ($data['gallery'] ?? []);
        $categories = array_map('intval', (array) ($data['categories'] ?? []));
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);
        ?>
        <div class="fa-card" dir="rtl">
            <div class="fa-card-header">
                <h2 class="fa-card-title"><?php esc_html_e('اطلاعات مشتری', 'fandoogh'); ?></h2>
                <div class="fa-customer-shortcode-reference">
                    <span><?php esc_html_e('نام:', 'fandoogh'); ?> <code>[fa_customer_name]</code></span>
                    <span><?php esc_html_e('تصویر شاخص:', 'fandoogh'); ?> <code>[fa_customer_image]</code></span>
                    <span><?php esc_html_e('دسته‌بندی مشتری:', 'fandoogh'); ?> <code>[fa_customer_categories]</code></span>
                </div>
            </div>

            <div class="fa-card-body">
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th>
                            <label for="fa_customer_excerpt"><?php esc_html_e('توضیحات کوتاه', 'fandoogh'); ?></label>
                            <code class="fa-field-shortcode">[fa_customer_description]</code>
                        </th>
                        <td>
                            <?php
                            wp_editor(
                                (string) ($data['excerpt'] ?? ''),
                                'fa_customer_excerpt',
                                [
                                    'textarea_name' => 'fa_customer_excerpt',
                                    'textarea_rows' => 10,
                                    'media_buttons' => true,
                                    'teeny' => false,
                                    'quicktags' => true,
                                    'tinymce' => [
                                        'wpautop' => true,
                                        'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignright,aligncenter,alignleft,link,unlink,undo,redo',
                                    ],
                                ]
                            );
                            ?>
                            <p class="description">
                                <?php esc_html_e('می‌توانید از تیتر، لینک، فهرست و سایر تگ‌های HTML امن استفاده کنید.', 'fandoogh'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label for="fa_customer_address"><?php esc_html_e('آدرس مشتری', 'fandoogh'); ?></label>
                            <code class="fa-field-shortcode">[fa_customer_address]</code>
                        </th>
                        <td>
                            <textarea id="fa_customer_address" name="fa_customer_address" rows="3" class="large-text"><?php
                                echo esc_textarea((string) ($data['address'] ?? ''));
                            ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <?php esc_html_e('دسته‌های محصول مرتبط', 'fandoogh'); ?>
                            <code class="fa-field-shortcode">[fa_customer_product_categories]</code>
                        </th>
                        <td class="fa-customer-product-categories">
                            <?php if (is_wp_error($terms) || $terms === []) : ?>
                                <p><?php esc_html_e('دسته محصولی یافت نشد.', 'fandoogh'); ?></p>
                            <?php else : ?>
                                <?php foreach ($terms as $term) : ?>
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="fa_customer_categories[]"
                                            value="<?php echo esc_attr((string) $term->term_id); ?>"
                                            <?php checked(in_array((int) $term->term_id, $categories, true)); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <?php esc_html_e('ویدئوی مشتری', 'fandoogh'); ?>
                            <code class="fa-field-shortcode">[fa_customer_video]</code>
                        </th>
                        <td>
                            <input
                                type="hidden"
                                id="fa_customer_video"
                                name="fa_customer_video"
                                value="<?php echo esc_attr((string) ($data['video'] ?? '')); ?>">
                            <button type="button" class="button" id="fa-upload-video"><?php esc_html_e('انتخاب ویدئو', 'fandoogh'); ?></button>
                            <button type="button" class="button" id="fa-remove-video"><?php esc_html_e('حذف ویدئو', 'fandoogh'); ?></button>
                            <div id="fa-video-preview">
                                <?php if (! empty($data['video'])) : ?>
                                    <video controls class="fa-video-preview-player">
                                        <source src="<?php echo esc_url((string) $data['video']); ?>">
                                    </video>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <?php esc_html_e('گالری تصاویر', 'fandoogh'); ?>
                            <code class="fa-field-shortcode">[fa_customer_gallery]</code>
                        </th>
                        <td>
                            <input
                                type="hidden"
                                id="fa_customer_gallery"
                                name="fa_customer_gallery"
                                value="<?php echo esc_attr(implode(',', array_map('intval', $gallery))); ?>">
                            <button type="button" class="button" id="fa-upload-gallery"><?php esc_html_e('انتخاب تصاویر', 'fandoogh'); ?></button>
                            <button type="button" class="button" id="fa-remove-gallery"><?php esc_html_e('حذف تصاویر', 'fandoogh'); ?></button>
                            <div id="fa-gallery-preview">
                                <?php foreach ($gallery as $imageId) : ?>
                                    <?php $image = wp_get_attachment_image_url((int) $imageId, 'thumbnail'); ?>
                                    <?php if ($image) : ?>
                                        <div class="fa-gallery-item"><img src="<?php echo esc_url($image); ?>" alt=""></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    public function save(int $postId): void
    {
        if (
            ! isset($_POST['fa_customer_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['fa_customer_nonce'])),
                'fa_customer_nonce'
            )
            || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            || ! current_user_can('edit_post', $postId)
        ) {
            return;
        }

        $gallery = array_values(array_filter(array_map(
            'absint',
            explode(',', sanitize_text_field(
                wp_unslash($_POST['fa_customer_gallery'] ?? '')
            ))
        ), 'wp_attachment_is_image'));

        $categories = array_values(array_filter(array_map(
            'absint',
            (array) wp_unslash($_POST['fa_customer_categories'] ?? [])
        ), static fn (int $termId): bool => (bool) term_exists($termId, 'product_cat')));

        CustomerManager::save($postId, [
            'excerpt' => wp_unslash($_POST['fa_customer_excerpt'] ?? ''),
            'address' => wp_unslash($_POST['fa_customer_address'] ?? ''),
            'video' => wp_unslash($_POST['fa_customer_video'] ?? ''),
            'gallery' => $gallery,
            'categories' => $categories,
        ]);
    }
}
