<?php

declare(strict_types=1);

use Fandoogh\Core\Constants\Meta\ReviewMeta;
use Fandoogh\Core\JalaliDate;

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

$reviewDate = '—';
if (!empty($review->comment_date_gmt) && $review->comment_date_gmt !== '0000-00-00 00:00:00') {
    try {
        $reviewDate = JalaliDate::formatUtc((string) $review->comment_date_gmt, false);
    } catch (\InvalidArgumentException) {
        // A malformed legacy timestamp must not leak a Gregorian fallback.
    }
}

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

            <?php echo esc_html($reviewDate); ?>

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
