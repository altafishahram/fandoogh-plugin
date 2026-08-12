<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants;

defined('ABSPATH') || exit;

/**
 * Fandoogh Options
 *
 * Central registry for all plugin option keys.
 *
 * @package Fandoogh\Core\Constants
 */
final class Options
{
    private function __construct()
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Plugin
    |--------------------------------------------------------------------------
    */

    /**
     * Enabled modules.
     */
    public const MODULES = 'fa_modules';

    /**
     * Plugin settings.
     */
    public const SETTINGS = 'fa_settings';

    public const DELETE_DATA_ON_UNINSTALL = 'fa_delete_data_on_uninstall';

    public const ADMIN_THEME_SETTINGS = 'fa_admin_theme_settings';

    public const ADMIN_THEME_ASSET = 'fa_admin_theme_asset';

    public const ADMIN_THEME_SCHEMA_VERSION = 'fa_admin_theme_schema_version';

    public const ADMIN_THEME_GENERATION_LOCK = 'fa_admin_theme_generation_lock';

    public const CALCULATOR_FIXED_PRICES = 'fa_calculator_fixed_prices';

    public const CALCULATOR_SETTINGS = 'fa_calculator_settings';

    /**
     * Plugin version.
     */
    public const VERSION = 'fa_version';

    public const FRAMEWORK_VERSION = 'fa_framework_version';

    public const DATABASE_VERSION = 'fa_db_version';

    public const MIGRATION_LOCK = 'fa_migration_lock';

    /**
     * Plugin build.
     */
    public const BUILD = 'fa_build';

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    /**
     * Cache version.
     */
    public const CACHE_VERSION = 'fa_cache_version';

    /**
     * Flush cache flag.
     */
    public const CACHE_FLUSH = 'fa_cache_flush';

    /*
    |--------------------------------------------------------------------------
    | Elementor
    |--------------------------------------------------------------------------
    */

    /**
     * Elementor integration settings.
     */
    public const ELEMENTOR = 'fa_elementor';

    /*
    |--------------------------------------------------------------------------
    | License
    |--------------------------------------------------------------------------
    */

    /**
     * License key.
     */
    public const LICENSE_KEY = 'fa_license_key';

    /**
     * License status.
     */
    public const LICENSE_STATUS = 'fa_license_status';

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    /**
     * Debug mode.
     */
    public const DEBUG = 'fa_debug';

    public const CUSTOMER_REWRITE_VERSION = 'fa_customer_rewrite_version';

    public const PROJECT_REWRITE_VERSION = 'fa_project_rewrite_version';
}
