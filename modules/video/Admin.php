<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Video;

defined('ABSPATH') || exit;

final class Admin
{
    /**
     * Boot module.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'product_cat_add_form_fields',
            [$this, 'addField']
        );

        add_action(
            'product_cat_edit_form_fields',
            [$this, 'editField']
        );

        add_action(
            'created_product_cat',
            [$this, 'save']
        );

        add_action(
            'edited_product_cat',
            [$this, 'save']
        );

        add_action(
            'admin_enqueue_scripts',
            [$this, 'enqueueAssets']
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        $screen = get_current_screen();

        if (
            ! $screen ||
            $screen->id !== 'edit-product_cat'
        ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script(
            'fa-video-admin',
            FA_URL . 'assets/admin/js/fa-video-admin.js',
            ['jquery'],
            FA_VERSION,
            true
        );

        wp_enqueue_style(
            'fa-video-admin',
            FA_URL . 'assets/admin/css/fa-video-admin.css',
            [],
            FA_VERSION
        );
    }

    /**
     * Add category field.
     *
     * @return void
     */
    public function addField(): void
    {
        wp_nonce_field('fa_save_video', 'fa_video_nonce');
        ?>

        <div class="form-field fa-video-wrap">

            <label>

                <?php esc_html_e(
                    'ویدیوی اختصاصی',
                    'fandoogh'
                ); ?>

            </label>

            <input
                type="url"
                id="fa_video"
                name="fa_video"
                class="regular-text fa-video-input"
                placeholder="https://example.com/video.mp4">

            <p>

                <button
                    type="button"
                    class="button button-secondary fa-video-upload">

                    <?php esc_html_e(
                        'انتخاب از رسانه',
                        'fandoogh'
                    ); ?>

                </button>

                <button
                    type="button"
                    class="button fa-video-remove">

                    <?php esc_html_e(
                        'حذف',
                        'fandoogh'
                    ); ?>

                </button>

            </p>

            <div class="fa-video-preview"></div>

        </div>

        <?php
    }

    /**
     * Edit category field.
     *
     * @param \WP_Term $term
     *
     * @return void
     */
    public function editField(
        \WP_Term $term
    ): void {

        wp_nonce_field('fa_save_video', 'fa_video_nonce');

        $video = Video::getUrl(
            $term->term_id
        );

        ?>

        <tr class="form-field">

            <th>

                <label>

                    <?php esc_html_e(
                        'ویدیوی اختصاصی',
                        'fandoogh'
                    ); ?>

                </label>

            </th>

            <td>

                <input
                    type="url"
                    id="fa_video"
                    name="fa_video"
                    class="regular-text fa-video-input"
                    value="<?php echo esc_attr($video); ?>">

                <p>

                    <button
                        type="button"
                        class="button button-secondary fa-video-upload">

                        <?php esc_html_e(
                            'انتخاب از رسانه',
                            'fandoogh'
                        ); ?>

                    </button>

                    <button
                        type="button"
                        class="button fa-video-remove">

                        <?php esc_html_e(
                            'حذف',
                            'fandoogh'
                        ); ?>

                    </button>

                </p>

                <div class="fa-video-preview">

                    <?php if ($video !== '') : ?>

                        <video
                            controls
                            preload="metadata"
                            style="max-width:400px;border-radius:8px;">

                            <source src="<?php echo esc_url($video); ?>">

                        </video>

                    <?php endif; ?>

                </div>

            </td>

        </tr>

        <?php
    }

    /**
     * Save video.
     *
     * @param int $termId
     *
     * @return void
     */
    public function save(
        int $termId
    ): void {

        if (
            ! isset($_POST['fa_video_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['fa_video_nonce'])),
                'fa_save_video'
            )
        ) {
            return;
        }

        $taxonomy = get_taxonomy('product_cat');

        if (
            ! $taxonomy instanceof \WP_Taxonomy
            || ! current_user_can($taxonomy->cap->manage_terms)
            || ! isset($_POST['fa_video'])
        ) {
            return;
        }

        Video::save(
            $termId,
            [
                'url' => wp_unslash(
                    $_POST['fa_video']
                ),
            ]
        );
    }
}
