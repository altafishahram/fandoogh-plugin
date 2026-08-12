<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Video;

defined('ABSPATH') || exit;

/**
 * Frontend
 *
 * Handles frontend functionality for the Video module.
 *
 * @package Fandoogh\Modules\Video
 */
final class Frontend
{
    /**
     * Boot frontend.
     *
     * @return void
     */
    public function boot(): void
    {
        // Frontend hooks will be registered here.
    }

    /**
     * Get video URL.
     *
     * @param int $termId
     *
     * @return string
     */
    public static function get(int $termId): string
    {
        return Video::getUrl($termId);
    }

    /**
     * Check if the term has a video.
     *
     * @param int $termId
     *
     * @return bool
     */
    public static function hasVideo(int $termId): bool
    {
        return Video::hasVideo($termId);
    }

    /**
     * Get complete video data.
     *
     * @param int $termId
     *
     * @return array
     */
    public static function data(int $termId): array
    {
        return Video::get($termId);
    }
}