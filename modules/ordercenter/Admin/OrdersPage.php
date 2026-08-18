<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Admin;

use Fandoogh\Modules\OrderCenter\Managers\OrderMetaManager;
use Fandoogh\Modules\OrderCenter\Managers\OrderQueryManager;
use Fandoogh\Modules\OrderCenter\Managers\OrderStatsManager;
use Fandoogh\Modules\OrderCenter\Module;

defined('ABSPATH') || exit;

final class OrdersPage
{
    private OrderQueryManager $queries;
    private OrderStatsManager $stats;
    private OrderMetaManager $meta;

    public function __construct()
    {
        $this->queries = new OrderQueryManager();
        $this->stats = new OrderStatsManager($this->queries);
        $this->meta = new OrderMetaManager();
    }

    public function render(): void
    {
        if (! Module::isAvailable()) {
            $this->dependencyNotice();
            return;
        }
        if (! current_user_can('manage_woocommerce')) {
            echo '<div class="notice notice-error"><p>دسترسی مدیریت سفارش‌ها را ندارید.</p></div>';
            return;
        }

        $page = $this;
        $orderId = $this->queryInt('order_id');
        if ($orderId > 0) {
            $order = $this->queries->get($orderId);
            if (! $order) {
                $this->notFound();
                return;
            }
            $meta = $this->meta->visible($order);
            $tracking = $this->meta->tracking($order);
            $notes = wc_get_order_notes(['order_id' => $orderId, 'type' => 'internal', 'limit' => 50]);
            include FA_MODULES . 'ordercenter/Admin/Views/order-single.php';
            return;
        }

        $search = $this->queryString('s');
        $status = $this->queryString('status');
        $currentPage = max(1, $this->queryInt('paged'));
        $result = $this->queries->paginate($currentPage, $search, $status);
        $dashboard = $this->stats->dashboard();
        $statusCounts = $this->queries->statusCounts();
        $statuses = $this->queries->statuses();
        include FA_MODULES . 'ordercenter/Admin/Views/dashboard.php';
    }

    public function money(float $amount, ?string $currency = null): string
    {
        $args = $currency !== null && $currency !== '' ? ['currency' => $currency] : [];
        return (string) wc_price($amount, $args);
    }

    public function statusClass(string $status): string
    {
        $status = sanitize_key($status);
        return 'fa-oc-status--' . ($status !== '' ? $status : 'unknown');
    }

    public function statusLabel(string $status): string
    {
        $normalized = $this->queries->normalizeStatus($status);
        $statuses = $this->queries->statuses();
        return isset($statuses['wc-' . $normalized]) ? (string) $statuses['wc-' . $normalized] : wc_get_order_status_name($normalized);
    }

    public function normalizeStatus(string $status): string { return $this->queries->normalizeStatus($status); }
    public function statuses(): array { return $this->queries->statuses(); }

    public function orderItemsSummary(\WC_Order $order): string
    {
        $names = [];
        foreach ($order->get_items('line_item') as $item) {
            $names[] = (string) $item->get_name();
            if (count($names) >= 2) { break; }
        }
        $count = count($order->get_items('line_item'));
        if ($count > 2) { $names[] = 'و ' . ($count - 2) . ' محصول دیگر'; }
        return implode('، ', array_map('esc_html', $names));
    }

    public function url(array $args = []): string { return Module::pageUrl($args); }

    public function queryString(string $key): string
    {
        $raw = $_GET[$key] ?? '';
        return is_scalar($raw) ? sanitize_text_field(wp_unslash($raw)) : '';
    }

    public function queryInt(string $key): int
    {
        $raw = $_GET[$key] ?? 0;
        return is_scalar($raw) ? absint(wp_unslash($raw)) : 0;
    }

    private function dependencyNotice(): void
    {
        echo '<section class="fa-oc-empty"><span class="dashicons dashicons-warning" aria-hidden="true"></span><h2>ووکامرس فعال نیست</h2><p>برای استفاده از داشبورد مرکز سفارشات، افزونه WooCommerce باید فعال باشد.</p></section>';
    }

    private function notFound(): void
    {
        echo '<section class="fa-oc-empty"><span class="dashicons dashicons-search" aria-hidden="true"></span><h2>سفارش پیدا نشد</h2><p><a class="button" href="' . esc_url($this->url()) . '">بازگشت به سفارش‌ها</a></p></section>';
    }
}
