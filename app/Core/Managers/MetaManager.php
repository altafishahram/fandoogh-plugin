<?php

declare(strict_types=1);

namespace Fandoogh\Core\Managers;

defined('ABSPATH') || exit;

final class MetaManager
{
    /**
     * Get post meta.
     */
    public static function getPostMeta(
        int $postId,
        string $key,
        mixed $default = null,
        bool $single = true
    ): mixed {
        $value = get_post_meta(
            $postId,
            $key,
            $single
        );

        return self::normalize(
            $value,
            $default
        );
    }

    /**
     * Update post meta.
     */
    public static function updatePostMeta(
        int $postId,
        string $key,
        mixed $value
    ): bool {
        return (bool) update_post_meta(
            $postId,
            $key,
            $value
        );
    }

    /**
     * Delete post meta.
     */
    public static function deletePostMeta(
        int $postId,
        string $key
    ): bool {
        return (bool) delete_post_meta(
            $postId,
            $key
        );
    }

    /**
     * Check post meta exists.
     */
    public static function hasPostMeta(
        int $postId,
        string $key
    ): bool {
        return metadata_exists(
            'post',
            $postId,
            $key
        );
    }

    /**
     * Get term meta.
     */
    public static function getTermMeta(
        int $termId,
        string $key,
        mixed $default = null,
        bool $single = true
    ): mixed {
        $value = get_term_meta(
            $termId,
            $key,
            $single
        );

        return self::normalize(
            $value,
            $default
        );
    }

    /**
     * Update term meta.
     */
    public static function updateTermMeta(
        int $termId,
        string $key,
        mixed $value
    ): bool {
        return (bool) update_term_meta(
            $termId,
            $key,
            $value
        );
    }

    /**
     * Delete term meta.
     */
    public static function deleteTermMeta(
        int $termId,
        string $key
    ): bool {
        return (bool) delete_term_meta(
            $termId,
            $key
        );
    }

    /**
     * Check term meta exists.
     */
    public static function hasTermMeta(
        int $termId,
        string $key
    ): bool {
        return metadata_exists(
            'term',
            $termId,
            $key
        );
    }

    /**
     * Get user meta.
     */
    public static function getUserMeta(
        int $userId,
        string $key,
        mixed $default = null,
        bool $single = true
    ): mixed {
        $value = get_user_meta(
            $userId,
            $key,
            $single
        );

        return self::normalize(
            $value,
            $default
        );
    }

    /**
     * Update user meta.
     */
    public static function updateUserMeta(
        int $userId,
        string $key,
        mixed $value
    ): bool {
        return (bool) update_user_meta(
            $userId,
            $key,
            $value
        );
    }

    /**
     * Delete user meta.
     */
    public static function deleteUserMeta(
        int $userId,
        string $key
    ): bool {
        return (bool) delete_user_meta(
            $userId,
            $key
        );
    }

    /**
     * Check user meta exists.
     */
    public static function hasUserMeta(
        int $userId,
        string $key
    ): bool {
        return metadata_exists(
            'user',
            $userId,
            $key
        );
    }
        /**
     * Get comment meta.
     */
    public static function getComment(
        int $commentId,
        string $key,
        mixed $default = null,
        bool $single = true
    ): mixed {
        $value = get_comment_meta(
            $commentId,
            $key,
            $single
        );

        return self::normalize(
            $value,
            $default
        );
    }

    /**
     * Update comment meta.
     */
    public static function updateComment(
        int $commentId,
        string $key,
        mixed $value
    ): bool {
        return (bool) update_comment_meta(
            $commentId,
            $key,
            $value
        );
    }

    /**
     * Delete comment meta.
     */
    public static function deleteComment(
        int $commentId,
        string $key
    ): bool {
        return (bool) delete_comment_meta(
            $commentId,
            $key
        );
    }

    /**
     * Check comment meta exists.
     */
    public static function hasComment(
        int $commentId,
        string $key
    ): bool {
        return metadata_exists(
            'comment',
            $commentId,
            $key
        );
    }

    /**
     * Get option.
     */
    public static function getOption(
        string $key,
        mixed $default = null
    ): mixed {
        return get_option(
            $key,
            $default
        );
    }

    /**
     * Update option.
     */
    public static function updateOption(
        string $key,
        mixed $value,
        bool $autoload = true
    ): bool {
        return update_option(
            $key,
            $value,
            $autoload
        );
    }

    /**
     * Delete option.
     */
    public static function deleteOption(
        string $key
    ): bool {
        return delete_option(
            $key
        );
    }

    /**
     * Normalize empty values.
     */
    private static function normalize(
        mixed $value,
        mixed $default
    ): mixed {
        if ($value === '' || $value === null) {
            return $default;
        }

        return $value;
    }
}