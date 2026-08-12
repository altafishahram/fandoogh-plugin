<?php

declare(strict_types=1);

namespace Fandoogh\Projects;

use Fandoogh\Core\Managers\ProjectManager;

defined('ABSPATH') || exit;

final class MetaBoxes
{
    public function boot(): void
    {
        add_action(
            'add_meta_boxes',
            [$this, 'register']
        );

        add_action(
            'save_post_fa_project',
            [$this, 'save']
        );
    }

    public function register(): void
    {
        add_meta_box(
            'fa_project_information',
            __('اطلاعات پروژه', 'fandoogh'),
            [$this, 'render'],
            'fa_project',
            'normal',
            'high'
        );
    }

    public function render(\WP_Post $post): void
    {
        wp_nonce_field(
            'fa_project_nonce',
            'fa_project_nonce'
        );

        $data = ProjectManager::all(
        $post->ID
        );

        $contractor = $data['contractor'] ?? '';

        $excerpt = $data['excerpt'] ?? '';

        $address = $data['address'] ?? '';

        $video = $data['video'] ?? '';

        $gallery = $data['gallery'] ?? [];

        $categories = $data['categories'] ?? [];

?>

<div class="fa-card">

    <div class="fa-card-header">

        <h2 class="fa-card-title">

            اطلاعات پروژه

        </h2>

    </div>

    <div class="fa-card-body">

        <table class="form-table">

            <tbody>

                <tr>

                    <th>

                        مجری پروژه

                    </th>

                    <td>

                        <input
                            type="text"
                            class="regular-text"
                            name="fa_project_contractor"
                            value="<?php echo esc_attr($contractor); ?>">

                    </td>

                </tr>

                <tr>

                    <th>

                        توضیحات کوتاه

                    </th>

                    <td>

                        <?php
                        wp_editor(
                            (string) $excerpt,
                            'fa_project_excerpt',
                            [
                                'textarea_name' => 'fa_project_excerpt',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => false,
                                'quicktags' => true,
                            ]
                        );
                        ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        آدرس پروژه

                    </th>

                    <td>

                        <textarea
                            name="fa_project_address"
                            rows="3"
                            class="large-text"><?php
                            echo esc_textarea(
                                $address
                            );
                        ?></textarea>

                    </td>

                </tr>

                <tr>

                    <th>

                        دسته‌بندی محصولات مرتبط

                    </th>

                    <td>

<?php

$terms = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
]);

if (!is_wp_error($terms)) :

    foreach ($terms as $term) :

?>

<label style="display:block;margin-bottom:8px;">

<input
type="checkbox"
name="fa_project_categories[]"
value="<?php echo esc_attr($term->term_id); ?>"

<?php checked(
in_array(
$term->term_id,
$categories
)
); ?>

>

<?php

echo esc_html(
$term->name
);

?>

</label>

<?php

endforeach;

endif;

?>

                    </td>

                </tr>
                                <tr>

                    <th>

                        ویدئو پروژه

                    </th>

                    <td>

                        <input
                            type="hidden"
                            id="fa_project_video"
                            name="fa_project_video"
                            value="<?php echo esc_attr($video); ?>">

                        <button
                            type="button"
                            class="button"
                            id="fa-upload-video">

                            انتخاب ویدئو

                        </button>

                        <button
                            type="button"
                            class="button"
                            id="fa-remove-video">

                            حذف

                        </button>

                        <div
                            id="fa-video-preview"
                            style="margin-top:20px;">

<?php if ($video) : ?>

<video
controls
style="max-width:420px;">

<source
src="<?php echo esc_url($video); ?>">

</video>

<?php endif; ?>

                        </div>

                    </td>

                </tr>

                <tr>

                    <th>

                        آلبوم تصاویر

                    </th>

                    <td>

                        <input
                            type="hidden"
                            id="fa_project_gallery"
                            name="fa_project_gallery"
                            value="<?php
                            echo esc_attr(
                                implode(',', $gallery)
                            );
                            ?>">

                        <button
                            type="button"
                            class="button"
                            id="fa-upload-gallery">

                            انتخاب تصاویر

                        </button>

                        <button
                            type="button"
                            class="button"
                            id="fa-remove-gallery">

                            حذف تصاویر

                        </button>

                        <div
                            id="fa-gallery-preview"
                            style="
                            margin-top:20px;
                            display:flex;
                            gap:10px;
                            flex-wrap:wrap;">

<?php

foreach ($gallery as $imageId) :

$image = wp_get_attachment_image_url(
$imageId,
'thumbnail'
);

if (!$image) {
    continue;
}

?>

<img
src="<?php echo esc_url($image); ?>"
style="
width:90px;
height:90px;
object-fit:cover;
border-radius:8px;
border:1px solid #ddd;
">

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
            !isset($_POST['fa_project_nonce'])
            || !wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST['fa_project_nonce'])
                ),
                'fa_project_nonce'
            )
        ) {
            return;
        }

        if (
            defined('DOING_AUTOSAVE')
            && DOING_AUTOSAVE
        ) {
            return;
        }

        if (
            !current_user_can(
                'edit_post',
                $postId
            )
        ) {
            return;
        }

        $gallery = [];

        if (!empty($_POST['fa_project_gallery'])) {

            $gallery = array_values(array_filter(array_map(
                'absint',
                explode(
                    ',',
                    sanitize_text_field(
                        wp_unslash(
                            $_POST['fa_project_gallery']
                        )
                    )
                )
            ), 'wp_attachment_is_image'));

        }

        $categories = [];

        if (!empty($_POST['fa_project_categories'])) {

            $categories = array_values(array_filter(array_map(
                'absint',
                wp_unslash(
                    (array) $_POST['fa_project_categories']
                )
            ), static fn (int $termId): bool => (bool) term_exists($termId, 'product_cat')));

        }

        ProjectManager::save(

            $postId,

            [

                'contractor' => sanitize_text_field(
                    wp_unslash(
                        $_POST['fa_project_contractor'] ?? ''
                    )
                ),

                'excerpt' => wp_unslash(
                    $_POST['fa_project_excerpt'] ?? ''
                ),

                'address' => sanitize_textarea_field(
                    wp_unslash(
                        $_POST['fa_project_address'] ?? ''
                    )
                ),

                'video' => esc_url_raw(
                    wp_unslash(
                        $_POST['fa_project_video'] ?? ''
                    )
                ),

                'gallery' => $gallery,

                'categories' => $categories,

            ]

        );
    }
}
