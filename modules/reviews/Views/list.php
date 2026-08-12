<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$reviews = $reviews ?? [];
$count = $count ?? 0;
$average = $average ?? 0;
$args = $args ?? [];

?>

<div class="fa-reviews">
    <?php if ($args['summary']) : ?>
        <div class="fa-reviews-summary">
            <div class="fa-review-average">
                <strong><?php echo esc_html(number_format((float) $average, 1)); ?></strong>
                <span><?php esc_html_e('از ۵', 'fandoogh'); ?></span>
            </div>

            <div class="fa-review-count">
                <?php
                printf(
                    esc_html__('%d نظر', 'fandoogh'),
                    (int) $count
                );
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($args['list']) : ?>
        <div class="fa-review-list">
            <?php if ($reviews !== []) : ?>
                <?php foreach ($reviews as $review) : ?>
                    <?php require __DIR__ . '/single.php'; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="fa-review-empty">
                    <?php esc_html_e('هنوز نظری ثبت نشده است.', 'fandoogh'); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($args['form']) : ?>
        <div class="fa-review-form-wrapper">
            <?php require __DIR__ . '/form.php'; ?>
        </div>
    <?php endif; ?>
</div>
