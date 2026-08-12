<?php

declare(strict_types=1);

use Fandoogh\Core\Constants\Meta\ReviewMeta;

defined('ABSPATH') || exit;

/*
|--------------------------------------------------------------------------
| Required variable
|--------------------------------------------------------------------------
|
| @var WP_Comment $review
|
*/

if (
    ! isset($review) ||
    ! $review instanceof WP_Comment
) {
    return;
}

$rating = (int) get_comment_meta(
    $review->comment_ID,
    ReviewMeta::RATING,
    true
);

?>

<article
    class="fa-review-item"
    id="review-<?php echo esc_attr((string) $review->comment_ID); ?>">

    <header class="fa-review-header">

        <strong class="fa-review-author">

            <?php echo esc_html(
                $review->comment_author
            ); ?>

        </strong>

        <span class="fa-review-date">

            <?php echo esc_html(
                get_comment_date(
                    '',
                    $review
                )
            ); ?>

        </span>

    </header>

    <div class="fa-review-rating">

        <?php for ($i = 1; $i <= 5; $i++) : ?>

            <span
                class="<?php echo esc_attr($i <= $rating
                    ? 'fa-star active'
                    : 'fa-star'); ?>">

                ★

            </span>

        <?php endfor; ?>

    </div>

    <div class="fa-review-content">

        <?php echo wpautop(
            wp_kses_post(
                $review->comment_content
            )
        ); ?>

    </div>

</article>
