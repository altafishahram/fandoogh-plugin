<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

defined('ABSPATH') || exit;

final class RateLimiter
{
    private const WINDOW = 900;
    private const LIMIT = 3;

    public function check(int $termId, string $email): true|\WP_Error
    {
        $key = $this->key($termId, $email);
        $attempts = (int) get_transient($key);
        $limit = (int) apply_filters(
            'fandoogh_reviews_rate_limit',
            self::LIMIT,
            $termId
        );

        if ($limit > 0 && $attempts >= $limit) {
            return new \WP_Error(
                'review_rate_limited',
                __('تعداد ارسال‌های شما بیش از حد مجاز است؛ لطفاً کمی بعد دوباره تلاش کنید.', 'fandoogh')
            );
        }

        return true;
    }

    public function hit(int $termId, string $email): void
    {
        $key = $this->key($termId, $email);
        $attempts = (int) get_transient($key);
        set_transient($key, $attempts + 1, self::WINDOW);
    }

    private function key(int $termId, string $email): string
    {
        $ip = sanitize_text_field(
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        );

        return 'fa_review_rate_' . hash_hmac(
            'sha256',
            $termId . '|' . strtolower($email) . '|' . $ip,
            wp_salt('nonce')
        );
    }
}
