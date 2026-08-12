<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class FixedPriceService
{
    public const PER_METER = 'per_meter';
    public const LUMP_SUM = 'lump_sum';

    public static function all(): array
    {
        return self::sanitize(FixedPriceRepository::all());
    }

    public static function save(array $items): array
    {
        $items = self::sanitize($items);
        FixedPriceRepository::save($items);

        return $items;
    }

    public static function activeForProduct(int $productId): array
    {
        if ($productId <= 0) {
            return [];
        }

        return array_values(array_filter(
            self::all(),
            static fn(array $item): bool => $item['enabled']
                && in_array($productId, $item['product_ids'], true)
        ));
    }

    public static function mappedProductIds(bool $activeOnly = true): array
    {
        $ids = [];

        foreach (self::all() as $item) {
            if ($activeOnly && ! $item['enabled']) {
                continue;
            }

            $ids = array_merge($ids, $item['product_ids']);
        }

        return array_values(array_unique(array_map('absint', $ids)));
    }

    public static function sanitize(array $items): array
    {
        $clean = [];
        $seenIds = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim(sanitize_text_field((string) ($item['title'] ?? '')));
            $price = max(0, (int) round((float) wc_format_decimal($item['price'] ?? 0)));
            $type = sanitize_key((string) ($item['type'] ?? ''));
            $type = in_array($type, [self::PER_METER, self::LUMP_SUM], true)
                ? $type
                : self::PER_METER;
            $productIds = array_values(array_unique(array_filter(
                array_map('absint', (array) ($item['product_ids'] ?? [])),
                static fn(int $id): bool => $id > 0 && get_post_type($id) === 'product'
            )));

            $mode = sanitize_key((string) ($item['mode'] ?? 'mandatory'));
            $mode = in_array($mode, ['mandatory', 'optional'], true) ? $mode : 'mandatory';

            if ($title === '' || $price <= 0) {
                continue;
            }

            $id = sanitize_key((string) ($item['id'] ?? ''));
            if ($id === '' || isset($seenIds[$id])) {
                $id = wp_generate_uuid4();
            }

            $seenIds[$id] = true;
            $clean[] = [
                'id' => $id,
                'title' => $title,
                'price' => $price,
                'type' => $type,
                'mode' => $mode,
                'product_ids' => $productIds,
                'enabled' => ! empty($item['enabled']),
            ];
        }

        return $clean;
    }
}
