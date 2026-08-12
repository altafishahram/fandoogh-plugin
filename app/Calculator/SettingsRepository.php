<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class SettingsRepository
{
    public static function get(): array
    {
        $settings = get_option(Options::CALCULATOR_SETTINGS, []);

        return is_array($settings) ? $settings : [];
    }

    public static function save(array $settings): bool
    {
        return update_option(Options::CALCULATOR_SETTINGS, $settings, false);
    }
}
