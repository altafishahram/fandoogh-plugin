<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

defined('ABSPATH') || exit;

final class Service
{
    public static function sanitize(mixed $value): array
    {
        $data = is_array($value) ? $value : [];

        return [
            'excerpt' => wp_kses_post((string) ($data['excerpt'] ?? '')),
            'address' => sanitize_textarea_field((string) ($data['address'] ?? '')),
            'video' => esc_url_raw((string) ($data['video'] ?? '')),
            'gallery' => array_values(array_filter(array_map('absint', (array) ($data['gallery'] ?? [])))),
            'categories' => array_values(array_filter(array_map('absint', (array) ($data['categories'] ?? [])))),
        ];
    }

    public static function save(int $postId, array $data): void
    {
        if (Repository::isCustomer($postId)) {
            Repository::save($postId, self::sanitize($data));
        }
    }
}
