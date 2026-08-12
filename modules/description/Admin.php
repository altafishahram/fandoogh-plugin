<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Description;

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

        wp_enqueue_style(
            'fa-description-admin',
            FA_URL . 'assets/admin/css/fa-description-admin.css',
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
        wp_nonce_field('fa_save_description', 'fa_description_nonce');
        ?>

        <div class="fa-card">

            <div class="fa-card-header">

                <h3 class="fa-card-title">

                    <?php esc_html_e(
                        'توضیحات اختصاصی',
                        'fandoogh'
                    ); ?>

                </h3>

            </div>

            <div class="fa-card-body">

                <?php

                wp_editor(
                    '',
                    'fa_description',
                    [
                        'textarea_name' => 'fa_description',
                        'textarea_rows' => 12,
                        'media_buttons' => false,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ]
                );

                ?>

                <p class="description">

                    <?php esc_html_e(
                        'این توضیحات توسط ویجت اختصاصی Fandoogh در صفحه دسته‌بندی نمایش داده خواهد شد.',
                        'fandoogh'
                    ); ?>

                </p>

            </div>

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

        wp_nonce_field('fa_save_description', 'fa_description_nonce');

        $value = Description::get(
            $term->term_id
        );

        ?>

        <tr class="form-field">

            <td colspan="2">

                <div class="fa-card">

                    <div class="fa-card-header">

                        <h3 class="fa-card-title">

                            <?php esc_html_e(
                                'توضیحات اختصاصی',
                                'fandoogh'
                            ); ?>

                        </h3>

                    </div>

                    <div class="fa-card-body">

                        <?php

                        wp_editor(
                            $value,
                            'fa_description',
                            [
                                'textarea_name' => 'fa_description',
                                'textarea_rows' => 12,
                                'media_buttons' => false,
                                'teeny'         => false,
                                'quicktags'     => true,
                            ]
                        );

                        ?>

                        <p class="description">

                            <?php esc_html_e(
                                'این محتوا توسط ویجت اختصاصی Fandoogh در صفحه دسته‌بندی نمایش داده خواهد شد.',
                                'fandoogh'
                            ); ?>

                        </p>

                    </div>

                </div>

            </td>

        </tr>

        <?php
    }

    /**
     * Save description.
     *
     * @param int $termId
     *
     * @return void
     */
    public function save(
        int $termId
    ): void {

        if (
            ! isset($_POST['fa_description_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['fa_description_nonce'])),
                'fa_save_description'
            )
        ) {
            return;
        }

        $taxonomy = get_taxonomy('product_cat');

        if (
            ! $taxonomy instanceof \WP_Taxonomy
            || ! current_user_can($taxonomy->cap->manage_terms)
            || ! isset($_POST['fa_description'])
        ) {
            return;
        }

        Description::save(
            $termId,
            wp_unslash(
                $_POST['fa_description']
            )
        );
    }
}
