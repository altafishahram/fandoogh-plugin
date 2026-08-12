<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\Taxonomies;

defined('ABSPATH') || exit;

/**
 * Reviews Ajax.
 *
 * Handles review Ajax requests.
 *
 * @package Fandoogh\Modules\Reviews
 */
final class Ajax
{
    /**
     * Boot.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'wp_ajax_fa_submit_review',
            [$this, 'submit']
        );

        add_action(
            'wp_ajax_nopriv_fa_submit_review',
            [$this, 'submit']
        );
    }

    /**
     * Submit review.
     *
     * @return void
     */
    public function submit(): void
    {
        if (! check_ajax_referer('fa_reviews', 'nonce', false)) {
            wp_send_json_error(
                ['message' => __('اعتبار فرم منقضی شده است؛ صفحه را تازه‌سازی کنید.', 'fandoogh')],
                403
            );
        }

        if (sanitize_text_field(wp_unslash($_POST['fa_guard'] ?? '')) !== '') {
            wp_send_json_error(
                ['message' => __('امکان ثبت این نظر وجود ندارد.', 'fandoogh')],
                400
            );
        }

        $termId = isset($_POST['term_id'])
            ? absint(wp_unslash($_POST['term_id']))
            : 0;

        if ($termId <= 0) {

            wp_send_json_error([
                'message' => __('دسته‌بندی معتبر نیست.', 'fandoogh'),
            ]);
        }

        $term = get_term($termId, Taxonomies::PRODUCT_CATEGORY);

        if (! $term instanceof \WP_Term) {
            wp_send_json_error([
                'message' => __('دسته‌بندی محصول معتبر نیست.', 'fandoogh'),
            ]);
        }

        $author = sanitize_text_field(
            wp_unslash($_POST['author'] ?? '')
        );

        $email = sanitize_email(
            wp_unslash($_POST['email'] ?? '')
        );

        $content = sanitize_textarea_field(
            wp_unslash($_POST['content'] ?? '')
        );

        $rating = max(
            1,
            min(5, (int) wp_unslash($_POST['rating'] ?? 5))
        );

        if ($author === '' || ! is_email($email) || $content === '') {
            wp_send_json_error([
                'message' => __('لطفاً تمام فیلدهای نظر را کامل کنید.', 'fandoogh'),
            ]);
        }

        $rateLimiter = new RateLimiter();
        $rateCheck = $rateLimiter->check($termId, $email);

        if (is_wp_error($rateCheck)) {
            wp_send_json_error([
                'message' => $rateCheck->get_error_message(),
            ]);
        }

        $result = Reviews::create(
            $termId,
            [
                'author'  => $author,
                'email'   => $email,
                'content' => $content,
                'rating'  => $rating,
            ]
        );

        if (is_wp_error($result)) {

            wp_send_json_error([
                'message' => $result->get_error_message(),
            ]);
        }

        $rateLimiter->hit($termId, $email);

        wp_send_json_success([
            'message' => __(
                'نظر شما ثبت شد و در انتظار تأیید است.',
                'fandoogh'
            ),

            'comment_id' => $result,
        ]);
    }
}
