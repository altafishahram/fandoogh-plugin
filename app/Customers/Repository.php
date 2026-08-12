<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\CustomerMeta;

defined('ABSPATH') || exit;

final class Repository
{
    public static function get(int $postId): array
    {
        $data = get_post_meta($postId, CustomerMeta::DATA, true);
        return is_array($data) ? $data : [];
    }

    public static function save(int $postId, array $data): void
    {
        update_post_meta($postId, CustomerMeta::DATA, $data);
    }

    public static function field(int $postId, string $field, mixed $default = null): mixed
    {
        $data = self::get($postId);
        return $data[$field] ?? $default;
    }

    public static function isCustomer(int $postId): bool
    {
        return get_post_type($postId) === ContentTypes::CUSTOMER;
    }
}
