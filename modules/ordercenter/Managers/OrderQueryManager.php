<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Managers;

defined('ABSPATH') || exit;

final class OrderQueryManager
{
    public const PER_PAGE = 20;

    public function statuses(): array { return function_exists('wc_get_order_statuses') ? wc_get_order_statuses() : []; }

    public function normalizeStatus(string $status): string
    {
        $status = sanitize_key($status);
        return str_starts_with($status, 'wc-') ? substr($status, 3) : $status;
    }

    public function isValidStatus(string $status): bool
    {
        $status = $this->normalizeStatus($status);
        foreach (array_keys($this->statuses()) as $key) {
            if ($this->normalizeStatus((string) $key) === $status) { return true; }
        }
        return false;
    }

    public function paginate(int $page = 1, string $search = '', string $status = ''): array
    {
        $page = max(1, $page);
        $args = ['limit' => self::PER_PAGE, 'page' => $page, 'paginate' => true, 'orderby' => 'date', 'order' => 'DESC', 'return' => 'objects'];
        if (($search = trim($search)) !== '') { $args['search'] = $search; }
        if ($status !== '' && $this->isValidStatus($status)) { $args['status'] = [$this->normalizeStatus($status)]; }
        $result = wc_get_orders($args);
        if (is_object($result) && isset($result->orders)) {
            return ['orders' => is_array($result->orders) ? $result->orders : [], 'total' => absint($result->total ?? 0), 'pages' => max(1, absint($result->max_num_pages ?? 1)), 'page' => $page];
        }
        $orders = is_array($result) ? $result : [];
        return ['orders' => $orders, 'total' => count($orders), 'pages' => 1, 'page' => 1];
    }

    public function get(int $orderId): ?\WC_Order
    {
        $order = $orderId > 0 ? wc_get_order($orderId) : false;
        return $order instanceof \WC_Order ? $order : null;
    }

    public function statusCounts(): array
    {
        $counts = [];
        foreach (array_keys($this->statuses()) as $key) {
            $status = $this->normalizeStatus((string) $key);
            $result = wc_get_orders(['status' => [$status], 'limit' => 1, 'paginate' => true, 'return' => 'ids']);
            $counts[$status] = is_object($result) ? absint($result->total ?? 0) : (is_array($result) ? count($result) : 0);
        }
        return $counts;
    }

    public function rangeStats(string $start): array
    {
        $all = wc_get_orders(['date_created' => '>' . $start, 'limit' => 1, 'paginate' => true, 'return' => 'ids']);
        $ordersCount = is_object($all) ? absint($all->total ?? 0) : (is_array($all) ? count($all) : 0);
        $excluded = ['failed', 'cancelled'];
        $statuses = [];
        foreach (array_keys($this->statuses()) as $key) {
            $status = $this->normalizeStatus((string) $key);
            if (! in_array($status, $excluded, true)) { $statuses[] = $status; }
        }
        $revenue = 0.0;
        $revenueOrders = 0;
        $page = 1;
        do {
            $result = wc_get_orders(['date_created' => '>' . $start, 'status' => $statuses, 'limit' => 100, 'page' => $page, 'paginate' => true, 'return' => 'ids']);
            $ids = is_object($result) && isset($result->orders) ? (array) $result->orders : (is_array($result) ? $result : []);
            foreach ($ids as $id) {
                $order = $this->get(absint($id));
                if (! $order) { continue; }
                $total = (float) $order->get_total() - (float) $order->get_total_refunded();
                $revenue += max(0.0, $total);
                $revenueOrders++;
            }
            $pages = is_object($result) ? max(1, absint($result->max_num_pages ?? 1)) : 1;
            $page++;
        } while ($page <= $pages && $ids !== []);
        return ['orders' => $ordersCount, 'revenue' => $revenue, 'average' => $revenueOrders > 0 ? $revenue / $revenueOrders : 0.0];
    }
}
