<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

defined('ABSPATH') || exit;

final class Support
{
    public const MINIMUM_VERSION = '3.5.0';

    public static function isLoaded(): bool
    {
        return did_action('elementor/loaded') > 0
            && defined('ELEMENTOR_VERSION');
    }

    public static function isCompatible(): bool
    {
        return self::isLoaded()
            && version_compare(ELEMENTOR_VERSION, self::MINIMUM_VERSION, '>=');
    }

    public static function dynamicTagsHook(): string
    {
        return 'elementor/dynamic_tags/register';
    }
}
