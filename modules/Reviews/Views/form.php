<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

if (! isset($termId) || (int) $termId <= 0) {
    return;
}

?>

<form id="fa-review-form" class="fa-review-form" method="post">
    <?php wp_nonce_field('fa_reviews', 'nonce'); ?>

    <div
        id="fa-review-notice"
        class="fa-review-notice"
        role="status"
        aria-live="polite"></div>

    <input
        type="hidden"
        name="term_id"
        value="<?php echo esc_attr((string) $termId); ?>">

    <div class="fa-review-hp" aria-hidden="true" style="position:absolute;left:-9999px">
        <label for="fa-review-guard"><?php esc_html_e('این فیلد را خالی بگذارید', 'fandoogh'); ?></label>
        <input id="fa-review-guard" type="text" name="fa_guard" value="" tabindex="-1" autocomplete="off">
    </div>

    <div class="fa-field">
        <label for="fa-review-author"><?php esc_html_e('نام', 'fandoogh'); ?></label>
        <input id="fa-review-author" type="text" name="author" required>
    </div>

    <div class="fa-field">
        <label for="fa-review-email"><?php esc_html_e('ایمیل', 'fandoogh'); ?></label>
        <input id="fa-review-email" type="email" name="email" required>
    </div>

    <div class="fa-field">
        <label for="fa-review-rating"><?php esc_html_e('امتیاز', 'fandoogh'); ?></label>
        <select id="fa-review-rating" name="rating">
            <?php for ($i = 5; $i >= 1; $i--) : ?>
                <option value="<?php echo esc_attr((string) $i); ?>">
                    <?php echo esc_html((string) $i); ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="fa-field">
        <label for="fa-review-content"><?php esc_html_e('متن نظر', 'fandoogh'); ?></label>
        <textarea id="fa-review-content" name="content" rows="6" required></textarea>
    </div>

    <button type="submit" class="button button-primary">
        <?php esc_html_e('ثبت نظر', 'fandoogh'); ?>
    </button>
</form>
