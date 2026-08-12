<?php
/**
 * Plugin Name: Fandoogh Framework
 * Plugin URI: https://fandoogh.ir
 * Description: فریم‌ورک حرفه‌ای فندق برای وردپرس، ووکامرس و المنتور.
 * Version: 1.0.0
 * Requires at least: 6.8
 * Requires PHP: 8.2
 * Requires Plugins: woocommerce
 * Author: فندق
 * Author URI: https://fandoogh.ir
 * Text Domain: fandoogh
 * Domain Path: /languages
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('FA_VERSION', '1.0.0');
define('FA_BUILD', '20260812-001');

define('FA_FILE', __FILE__);
define('FA_PATH', plugin_dir_path(__FILE__));
define('FA_URL', plugin_dir_url(__FILE__));
define('FA_APP', FA_PATH . 'app/');
define('FA_MODULES', FA_PATH . 'modules/');
define('FA_ASSETS', FA_PATH . 'assets/');
define('FA_CONFIG', FA_PATH . 'config/');

require_once FA_APP . 'Core/Autoloader.php';

Fandoogh\Core\Autoloader::register();

require_once FA_APP . 'Core/Helpers.php';
require_once FA_APP . 'Core/Activator.php';
require_once FA_APP . 'Core/Deactivator.php';

register_activation_hook(__FILE__, [Fandoogh\Core\Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [Fandoogh\Core\Deactivator::class, 'deactivate']);

add_action(
    'before_woocommerce_init',
    static function (): void {
        $features = \Automattic\WooCommerce\Utilities\FeaturesUtil::class;
        if (class_exists($features)) {
            $features::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }
);

Fandoogh\Core\Application::instance();
