<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Description;

defined('ABSPATH') || exit;

/**
 * Frontend
 *
 * Handles frontend functionality for the Description module.
 *
 * @package Fandoogh\Modules\Description
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
        // Reserved for future frontend hooks.
    }

    /**
     * Get description.
     *
     * @param int $termId
     *
     * @return string
     */
    public static function get(int $termId): string
    {
        return Description::get($termId);
    }

    /**
     * Check if description exists.
     *
     * @param int $termId
     *
     * @return bool
     */
    public static function hasDescription(int $termId): bool
    {
        return Description::exists($termId);
    }
}