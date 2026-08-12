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

    public const ADMIN_UI = 'fa-admin-ui';

    public const FRONTEND = 'fa-frontend';

    public const DESCRIPTION = 'fa-description';

    public const VIDEO = 'fa-video';

    public const GALLERY = 'fa-gallery';

    public const FAQ = 'fa-faq';

    public const REVIEWS = 'fa-reviews';

    public const ELEMENTOR = 'fa-elementor';

    public const CALCULATOR_ADMIN = 'fa-calculator-admin';

    public const FANDOOGH_CALCULATOR = 'fa-fandoogh-calculator';

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

    public const ADMIN_UI_CSS = 'assets/css/admin-ui.css';

    public const FRONTEND_CSS = 'assets/css/frontend.css';

    public const DESCRIPTION_CSS = 'assets/css/description.css';

    public const VIDEO_CSS = 'assets/css/video.css';

    public const GALLERY_CSS = 'assets/css/gallery.css';

    public const FAQ_CSS = 'assets/css/fa-faq-admin.css';

    public const REVIEWS_CSS = 'assets/css/reviews.css';

    public const ELEMENTOR_CSS = 'assets/css/elementor.css';

    public const CALCULATOR_ADMIN_CSS = 'assets/admin/css/calculator.css';

    public const FANDOOGH_CALCULATOR_CSS = 'assets/frontend/css/fandoogh-calculator.css';

    /*
    |--------------------------------------------------------------------------
    | JavaScript
    |--------------------------------------------------------------------------
    */

    public const DESCRIPTION_JS = 'assets/js/description.js';

    public const VIDEO_JS = 'assets/js/video.js';

    public const VIDEO_ADMIN_JS = 'assets/js/fa-video-admin.js';

    public const GALLERY_JS = 'assets/js/gallery.js';

    public const FAQ_JS = 'assets/js/fa-faq-admin.js';

    public const REVIEWS_JS = 'assets/js/reviews.js';

    public const ELEMENTOR_JS = 'assets/js/elementor.js';

    public const CALCULATOR_ADMIN_JS = 'assets/admin/js/calculator.js';

    public const FANDOOGH_CALCULATOR_JS = 'assets/frontend/js/fandoogh-calculator.js';
}
