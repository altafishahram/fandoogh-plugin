<?php

declare(strict_types=1);

namespace Fandoogh\Modules\OrderCenter\Managers;

defined('ABSPATH') || exit;

final class OrderStatsManager
{
    public function __construct(private readonly OrderQueryManager $queries) {}

    public function dashboard(): array
    {
        $stats = [];
        foreach ([1, 7, 30] as $days) { $stats[(string) $days] = $this->cachedRange($days); }
        return $stats;
    }

    public function invalidate(): void
    {
        foreach ([1, 7, 30] as $days) { delete_transient('fa_oc_stats_' . $days); }
    }

    private function cachedRange(int $days): array
    {
        $key = 'fa_oc_stats_' . $days;
        $cached = get_transient($key);
        if (is_array($cached) && isset($cached['orders'], $cached['revenue'], $cached['average'])) {
            return ['orders' => absint($cached['orders']), 'revenue' => (float) $cached['revenue'], 'average' => (float) $cached['average']];
        }
        $now = new \DateTimeImmutable('now', wp_timezone());
        $start = $days === 1 ? $now->setTime(0, 0, 0) : $now->modify('-' . ($days - 1) . ' days')->setTime(0, 0, 0);
        $stats = $this->queries->rangeStats($start->format('Y-m-d H:i:s'));
        set_transient($key, $stats, MINUTE_IN_SECONDS);
        return $stats;
    }
}
