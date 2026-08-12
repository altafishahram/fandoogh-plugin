<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\Meta\ReviewMeta;
use Fandoogh\Core\Managers\MetaManager;
use Fandoogh\Core\Managers\ReviewProxyManager;

defined('ABSPATH') || exit;

final class Repository
{
    public function proxyId(int $termId): int
    {
        return ReviewProxyManager::get($termId);
    }

    /** @return array<int, \WP_Comment> */
    public function approved(int $termId): array
    {
        $proxyId = $this->proxyId($termId);

        if ($proxyId <= 0) {
            return [];
        }

        return get_comments([
            'post_id' => $proxyId,
            'status' => 'approve',
            'orderby' => 'comment_date',
            'order' => 'DESC',
        ]);
    }

    public function insert(int $termId, array $data): int|\WP_Error
    {
        $proxyId = $this->proxyId($termId);

        if ($proxyId <= 0) {
            return new \WP_Error(
                'review_proxy_error',
                __('امکان آماده‌سازی محل ثبت نظر وجود ندارد.', 'fandoogh')
            );
        }

        $commentId = wp_new_comment([
            'comment_post_ID' => $proxyId,
            'comment_author' => $data['author'],
            'comment_author_email' => $data['email'],
            'comment_content' => $data['content'],
            'comment_approved' => 0,
        ], true);

        if (is_wp_error($commentId)) {
            return $commentId;
        }

        if (! $commentId) {
            return new \WP_Error(
                'review_error',
                __('ثبت نظر انجام نشد.', 'fandoogh')
            );
        }

        $status = wp_get_comment_status((int) $commentId);

        if (! in_array($status, ['spam', 'trash'], true)) {
            $held = wp_set_comment_status(
                (int) $commentId,
                'hold',
                true
            );

            if (is_wp_error($held)) {
                wp_delete_comment((int) $commentId, true);
                return $held;
            }
        }

        MetaManager::updateComment(
            (int) $commentId,
            ReviewMeta::RATING,
            $data['rating']
        );

        return (int) $commentId;
    }

    public function delete(int $commentId): bool
    {
        return wp_delete_comment($commentId, true);
    }
}
