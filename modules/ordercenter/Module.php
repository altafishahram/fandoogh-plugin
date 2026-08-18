<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter;

use Fandoogh\Core\Application;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\Modules\OrderCenter\Admin\Actions;
use Fandoogh\Modules\OrderCenter\Admin\OrdersPage;

defined('ABSPATH') || exit;

final class Module
{
    public static function isAvailable(): bool
    {
        return function_exists('wc_get_orders') && function_exists('wc_get_order');
    }

    public function boot(): void
    {
        if (self::isAvailable()) {
            (new Actions())->boot();
        }
    }

    public static function pageUrl(array $args = []): string
    {
        return add_query_arg(array_merge(['page' => 'fa-order-center'], $args), admin_url('admin.php'));
    }

    public static function render(): void
    {
        $modules = Application::instance()->get('modules');
        if (! $modules instanceof ModuleManager || ! $modules->enabled('order-center')) {
            echo '<div class="notice notice-warning"><p>ماژول مرکز سفارشات فعال نیست.</p></div>';
            return;
        }
        (new OrdersPage())->render();
    }
}
