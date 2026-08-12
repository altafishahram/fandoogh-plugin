<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\Taxonomies;

use Fandoogh\Core\Constants\Meta\ReviewMeta;
use Fandoogh\Core\Managers\MetaManager;

defined('ABSPATH') || exit;

final class Service
{
    public function __construct(
        private readonly Repository $repository,
        private readonly Cache $cache
    ) {
    }

    public function create(int $termId, array $data): int|\WP_Error
    {
        $term = get_term($termId, Taxonomies::PRODUCT_CATEGORY);

        if (! $term instanceof \WP_Term) {
            return new \WP_Error(
                'invalid_review_category',
                __('دسته‌بندی محصول معتبر نیست.', 'fandoogh')
            );
        }

        $clean = [
            'author' => wp_html_excerpt(sanitize_text_field((string) ($data['author'] ?? '')), 100, ''),
            'email' => sanitize_email((string) ($data['email'] ?? '')),
            'content' => wp_html_excerpt(sanitize_textarea_field((string) ($data['content'] ?? '')), 5000, ''),
            'rating' => max(1, min(5, (int) ($data['rating'] ?? 0))),
        ];

        if (
            $clean['author'] === ''
            || ! is_email($clean['email'])
            || $clean['content'] === ''
        ) {
            return new \WP_Error(
                'invalid_review_data',
                __('لطفاً تمام فیلدهای نظر را کامل کنید.', 'fandoogh')
            );
        }

        $result = $this->repository->insert($termId, $clean);

        if (! is_wp_error($result)) {
            $this->cache->forget($termId);
        }

        return $result;
    }

    /** @return array<int, \WP_Comment> */
    public function reviews(int $termId): array
    {
        return $this->repository->approved($termId);
    }

    /** @return array{count:int,average:float} */
    public function stats(int $termId): array
    {
        $cached = $this->cache->get($termId);

        if ($cached !== null) {
            return $cached;
        }

        $reviews = $this->reviews($termId);
        $count = count($reviews);
        $total = 0;

        foreach ($reviews as $review) {
            $total += (int) MetaManager::getComment(
                (int) $review->comment_ID,
                ReviewMeta::RATING,
                0
            );
        }

        $average = $count > 0 ? round($total / $count, 1) : 0.0;
        $this->cache->put($termId, $count, $average);

        return ['count' => $count, 'average' => $average];
    }

    public function delete(int $commentId): bool
    {
        return $this->repository->delete($commentId);
    }
}
