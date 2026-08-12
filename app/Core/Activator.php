<?php

declare(strict_types=1);

namespace Fandoogh\Core;

use Fandoogh\Core\Constants\Options;
use Fandoogh\AdminTheme\SettingsSchema;
use Fandoogh\AdminTheme\ThemeManager;

defined('ABSPATH') || exit;

final class Activator
{
    /**
     * Run plugin activation tasks.
     */
    public static function activate(): void
    {
        add_option(Options::FRAMEWORK_VERSION, FA_VERSION);
        add_option(Options::VERSION, FA_VERSION);
        add_option(Options::BUILD, FA_BUILD);
        add_option(Options::DATABASE_VERSION, '0.0.0');
        add_option(Options::DELETE_DATA_ON_UNINSTALL, false, '', false);
        add_option(Options::ADMIN_THEME_SETTINGS, SettingsSchema::defaults(), '', false);
        add_option(Options::CALCULATOR_FIXED_PRICES, [], '', false);

        add_option(
            Options::MODULES,
            Config::load('modules')
        );

        (new ThemeManager())->ensure();
    }
}
