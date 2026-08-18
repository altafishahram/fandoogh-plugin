<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants;

defined('ABSPATH') || exit;

/**
 * Fandoogh Assets
 *
 * Central registry for all asset handles and paths.
 *
 * @package Fandoogh\Core\Constants
 */
final class Assets
{
    private function __construct()
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Asset Handles
    |--------------------------------------------------------------------------
    */

    public const ADMIN = 'fa-admin';

    public const ADMIN_DASHBOARD = 'fa-dashboard';

    public const ADMIN_MODULES = 'fa-admin-modules';

    public const ADMIN_THEME = 'fa-admin-theme';

    public const ADMIN_THEME_MANAGER = 'fa-admin-theme-manager';

    public const REVIEWS = 'fa-reviews';

    public const CALCULATOR_ADMIN = 'fa-calculator-admin';

    public const CALCULATOR = 'fa-calculator';

    /** @deprecated Use CALCULATOR. Kept as an internal compatibility alias. */
    public const FANDOOGH_CALCULATOR = self::CALCULATOR;

    public const ORDER_CENTER_ADMIN = 'fa-order-center-admin';

    /*
    |--------------------------------------------------------------------------
    | CSS
    |--------------------------------------------------------------------------
    */

    public const ADMIN_CSS = 'assets/admin/css/admin.css';

    public const ADMIN_DASHBOARD_JS = 'assets/admin/js/dashboard.js';

    public const ADMIN_MODULES_CSS = 'assets/admin/css/modules.css';

    public const ADMIN_MODULES_JS = 'assets/admin/js/modules.js';

    public const ADMIN_THEME_MANAGER_JS = 'assets/admin/js/theme-manager.js';

    public const CALCULATOR_ADMIN_CSS = 'assets/admin/css/calculator.css';

    public const CALCULATOR_CSS = 'assets/frontend/css/fandoogh-calculator.css';

    /** @deprecated Use CALCULATOR_CSS. */
    public const FANDOOGH_CALCULATOR_CSS = self::CALCULATOR_CSS;

    public const ORDER_CENTER_ADMIN_CSS = 'assets/admin/css/order-center.css';

    /*
    |--------------------------------------------------------------------------
    | JavaScript
    |--------------------------------------------------------------------------
    */

    public const CALCULATOR_ADMIN_JS = 'assets/admin/js/calculator.js';

    public const CALCULATOR_JS = 'assets/frontend/js/fandoogh-calculator.js';

    /** @deprecated Use CALCULATOR_JS. */
    public const FANDOOGH_CALCULATOR_JS = self::CALCULATOR_JS;

    public const ORDER_CENTER_ADMIN_JS = 'assets/admin/js/order-center.js';
}
