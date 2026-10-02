<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
defined('ABSPATH') || exit;
final class Service
{
    public static function sanitize(mixed $value): array {
        $d = is_array($value) ? $value : [];
        $customer = absint($d['customer_id'] ?? 0);
        return [
            'contractor' => sanitize_text_field((string) ($d['contractor'] ?? '')),
            'excerpt' => wp_kses_post((string) ($d['excerpt'] ?? '')),
            'address' => sanitize_textarea_field((string) ($d['address'] ?? '')),
            'video' => esc_url_raw((string) ($d['video'] ?? '')),
            'gallery' => array_values(array_filter(array_map('absint', (array) ($d['gallery'] ?? [])))),
            'categories' => array_values(array_filter(array_map('absint', (array) ($d['categories'] ?? [])))),
            'customer_id' => get_post_type($customer) === 'fa_customer' ? $customer : 0,
            'product_ids' => array_values(array_unique(array_filter(array_map('absint', (array) ($d['product_ids'] ?? [])), static fn (int $id): bool => get_post_type($id) === 'product'))),
        ];
    }
    public static function save(int $id,array $data): void {
        if (!Repository::isProject($id)) return;
        $data = array_merge(Repository::get($id), $data);
        $clean = self::sanitize($data);
        Repository::save($id, $clean);
    }
}
