<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Admin;

use Fandoogh\Modules\OrderCenter\Managers\OrderQueryManager;
use Fandoogh\Modules\OrderCenter\Managers\OrderStatsManager;
use Fandoogh\Modules\OrderCenter\Services\OrderService;

defined('ABSPATH') || exit;

final class Actions
{
    public function boot(): void
    {
        add_action('admin_post_fa_order_center_update_status', [$this, 'updateStatus']);
        add_action('admin_post_fa_order_center_add_note', [$this, 'addNote']);
        add_action('woocommerce_order_status_changed', [$this, 'invalidateStats']);
        add_action('woocommerce_new_order', [$this, 'invalidateStats']);
    }

    public function updateStatus(): void
    {
        $orderId = $this->orderId();
        $this->authorize($orderId, 'status');
        $result = $this->service()->updateStatus($orderId, $this->scalarPost('order_status'));
        $this->redirect($orderId, $result, 'status');
    }

    public function addNote(): void
    {
        $orderId = $this->orderId();
        $this->authorize($orderId, 'note');
        $result = $this->service()->addNote($orderId, $this->scalarPost('order_note'));
        $this->redirect($orderId, $result, 'note');
    }

    public function invalidateStats(mixed ...$ignored): void
    {
        unset($ignored);
        (new OrderStatsManager(new OrderQueryManager()))->invalidate();
    }

    private function service(): OrderService
    {
        $queries = new OrderQueryManager();
        return new OrderService($queries, new OrderStatsManager($queries));
    }

    private function authorize(int $orderId, string $action): void
    {
        if (! current_user_can('manage_woocommerce')) {
            wp_die('دسترسی مدیریت سفارش‌ها را ندارید.', 'دسترسی غیرمجاز', ['response' => 403]);
        }
        check_admin_referer('fa_order_center_' . $action . '_' . $orderId);
    }

    private function orderId(): int
    {
        $raw = $_POST['order_id'] ?? 0;
        return is_scalar($raw) ? absint(wp_unslash($raw)) : 0;
    }

    private function scalarPost(string $key): string
    {
        $raw = $_POST[$key] ?? '';
        return is_scalar($raw) ? (string) wp_unslash($raw) : '';
    }

    private function redirect(int $orderId, true|\WP_Error $result, string $type): never
    {
        $args = ['order_id' => $orderId];
        if (is_wp_error($result)) {
            $args['fa_oc_error'] = $result->get_error_message();
        } else {
            $args['fa_oc_success'] = $type === 'status' ? 'وضعیت سفارش با موفقیت تغییر کرد.' : 'یادداشت داخلی ثبت شد.';
        }
        wp_safe_redirect(\Fandoogh\Modules\OrderCenter\Module::pageUrl($args));
        exit;
    }
}
