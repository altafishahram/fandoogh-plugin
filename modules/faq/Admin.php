<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

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

        wp_enqueue_editor();

        wp_enqueue_media();

        wp_enqueue_script(
            'fa-faq-admin',
            FA_URL . 'assets/admin/js/fa-faq-admin.js',
            ['jquery'],
            FA_VERSION,
            true
        );

        wp_enqueue_style(
            'fa-faq-admin',
            FA_URL . 'assets/admin/css/fa-faq-admin.css',
            [],
            FA_VERSION
        );
    }

    /**
     * Add field.
     *
     * @return void
     */
    public function addField(): void
    {
        wp_nonce_field(
            'fa_save_faq',
            'fa_faq_nonce'
        );

        ?>

        <div class="fa-card">

            <div class="fa-card-header">

                <h3 class="fa-card-title">

                    <?php esc_html_e(
                        'سوالات متداول',
                        'fandoogh'
                    ); ?>

                </h3>

            </div>

            <div
                class="fa-card-body"
                id="fa-faq-wrapper">

            </div>

            <p>

                <button
                    type="button"
                    class="button button-primary"
                    id="fa-add-faq">

                    <?php esc_html_e(
                        'افزودن سوال',
                        'fandoogh'
                    ); ?>

                </button>

            </p>

        </div>

        <?php
    }

    /**
     * Edit field.
     *
     * @param \WP_Term $term
     *
     * @return void
     */
    public function editField(
        \WP_Term $term
    ): void {

        wp_nonce_field(
            'fa_save_faq',
            'fa_faq_nonce'
        );

        $items = Faq::get(
            $term->term_id
        );

        ?>

        <tr class="form-field">

            <td colspan="2">

                <div class="fa-card">

                    <div class="fa-card-header">

                        <h3 class="fa-card-title">

                            <?php esc_html_e(
                                'سوالات متداول',
                                'fandoogh'
                            ); ?>

                        </h3>

                    </div>

                    <div
                        class="fa-card-body"
                        id="fa-faq-wrapper">

                    </div>

                    <script>

                        window.faFaqData =
                        <?php echo wp_json_encode($items); ?>;

                    </script>

                    <p>

                        <button
                            type="button"
                            class="button button-primary"
                            id="fa-add-faq">

                            <?php esc_html_e(
                                'افزودن سوال',
                                'fandoogh'
                            ); ?>

                        </button>

                    </p>

                </div>

            </td>

        </tr>

        <?php
    }

    /**
     * Save FAQ.
     *
     * @param int $termId
     *
     * @return void
     */
    public function save(
        int $termId
    ): void {

        if (
            ! isset($_POST['fa_faq_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST['fa_faq_nonce'])
                ),
                'fa_save_faq'
            )
        ) {
            return;
        }

        $taxonomy = get_taxonomy('product_cat');

        if (
            ! $taxonomy instanceof \WP_Taxonomy
            || ! current_user_can($taxonomy->cap->manage_terms)
        ) {
            return;
        }

        if (! isset($_POST['fa_faq'])) {
            return;
        }

        $data = json_decode(
            wp_unslash($_POST['fa_faq']),
            true
        );

        if (! is_array($data)) {
            $data = [];
        }

        Faq::save(
            $termId,
            $data
        );
    }
}
