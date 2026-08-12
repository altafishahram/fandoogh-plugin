<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Constants\Meta\ReviewMeta;

defined('ABSPATH') || exit;

final class Cache
{
    private const PREFIX = 'fa_review_stats_';

    public function boot(): void
    {
        add_action('comment_post', [$this, 'invalidateByComment'], 10, 1);
        add_action('edit_comment', [$this, 'invalidateByComment'], 10, 1);
        add_action('delete_comment', [$this, 'invalidateByComment'], 10, 1);
        add_action(
            'transition_comment_status',
            [$this, 'invalidateByStatus'],
            10,
            3
        );
        add_action(
            'updated_comment_meta',
            [$this, 'invalidateByMeta'],
            10,
            4
        );
    }

    /** @return array{count:int,average:float}|null */
    public function get(int $termId): ?array
    {
        $value = get_transient(self::PREFIX . $termId);

        return is_array($value) ? $value : null;
    }

    public function put(int $termId, int $count, float $average): void
    {
        set_transient(
            self::PREFIX . $termId,
            ['count' => $count, 'average' => $average],
            DAY_IN_SECONDS
        );
    }

    public function forget(int $termId): void
    {
        if ($termId > 0) {
            delete_transient(self::PREFIX . $termId);
        }
    }

    public function invalidateByComment(int $commentId): void
    {
        $this->forget($this->termIdForComment($commentId));
    }

    public function invalidateByStatus(
        string $newStatus,
        string $oldStatus,
        \WP_Comment $comment
    ): void {
        $this->invalidateByComment((int) $comment->comment_ID);
    }

    public function invalidateByMeta(
        int $metaId,
        int $commentId,
        string $metaKey,
        mixed $metaValue
    ): void {
        if ($metaKey === ReviewMeta::RATING) {
            $this->invalidateByComment($commentId);
        }
    }

    private function termIdForComment(int $commentId): int
    {
        $comment = get_comment($commentId);

        if (! $comment instanceof \WP_Comment) {
            return 0;
        }

        return (int) get_post_meta(
            (int) $comment->comment_post_ID,
            ReviewMeta::PROXY_OBJECT_ID,
            true
        );
    }
}
