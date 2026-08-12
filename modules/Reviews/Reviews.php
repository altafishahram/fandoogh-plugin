<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

defined('ABSPATH') || exit;

/**
 * Backward-compatible public facade for review consumers.
 */
final class Reviews
{
    private static ?Service $service = null;

    private static function service(): Service
    {
        if (self::$service === null) {
            self::$service = new Service(
                new Repository(),
                new Cache()
            );
        }

        return self::$service;
    }

    public static function get(int $termId): array
    {
        return self::service()->reviews($termId);
    }

    public static function create(int $termId, array $data): int|\WP_Error
    {
        return self::service()->create($termId, $data);
    }

    public static function delete(int $commentId): bool
    {
        return self::service()->delete($commentId);
    }

    public static function count(int $termId): int
    {
        return self::service()->stats($termId)['count'];
    }

    public static function average(int $termId): float
    {
        return self::service()->stats($termId)['average'];
    }

    public static function hasReviews(int $termId): bool
    {
        return self::count($termId) > 0;
    }

    public static function proxyId(int $termId): int
    {
        return (new Repository())->proxyId($termId);
    }
}
