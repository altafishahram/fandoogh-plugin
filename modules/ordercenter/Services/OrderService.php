<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Services;

use Fandoogh\Modules\OrderCenter\Managers\OrderQueryManager;
use Fandoogh\Modules\OrderCenter\Managers\OrderStatsManager;

defined('ABSPATH') || exit;

final class OrderService
{
    public function __construct(private readonly OrderQueryManager $queries, private readonly OrderStatsManager $stats) {}

    public function updateStatus(int $orderId, string $status): true|\WP_Error
    {
        $order = $this->queries->get($orderId);
        if (! $order) { return new \WP_Error('fa_oc_missing_order', 'سفارش پیدا نشد.'); }
        $status = $this->queries->normalizeStatus($status);
        if (! $this->queries->isValidStatus($status)) { return new \WP_Error('fa_oc_invalid_status', 'وضعیت انتخاب‌شده معتبر نیست.'); }
        $oldStatus = $order->get_status();
        try {
            $order->update_status($status, 'تغییر وضعیت از مرکز سفارشات فندوق', true);
        } catch (\Throwable $exception) {
            if (function_exists('wc_get_logger')) { wc_get_logger()->error('Order status update failed: ' . $exception->getMessage(), ['source' => 'fandoogh-order-center']); }
            return new \WP_Error('fa_oc_status_update_failed', 'تغییر وضعیت سفارش انجام نشد.');
        }
        $this->stats->invalidate();
        do_action('fandoogh_order_center_order_status_updated', $order, $oldStatus, $status);
        return true;
    }

    public function addNote(int $orderId, string $note): true|\WP_Error
    {
        $order = $this->queries->get($orderId);
        if (! $order) { return new \WP_Error('fa_oc_missing_order', 'سفارش پیدا نشد.'); }
        $note = trim(sanitize_textarea_field($note));
        if ($note === '') { return new \WP_Error('fa_oc_empty_note', 'متن یادداشت را وارد کنید.'); }
        $note = function_exists('mb_substr') ? mb_substr($note, 0, 2000) : substr($note, 0, 2000);
        try {
            $order->add_order_note($note, false, true);
        } catch (\Throwable $exception) {
            if (function_exists('wc_get_logger')) { wc_get_logger()->error('Order note creation failed: ' . $exception->getMessage(), ['source' => 'fandoogh-order-center']); }
            return new \WP_Error('fa_oc_note_failed', 'ثبت یادداشت انجام نشد.');
        }
        do_action('fandoogh_order_center_order_note_added', $order, $note);
        return true;
    }
}
