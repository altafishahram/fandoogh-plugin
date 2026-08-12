<?php

declare(strict_types=1);

namespace Fandoogh\Core\Managers;

use Fandoogh\Customers\Repository;
use Fandoogh\Customers\Service;

defined('ABSPATH') || exit;

/** Backward-compatible facade for customer integrations. */
final class CustomerManager
{
    public static function isCustomer(int $postId): bool { return Repository::isCustomer($postId); }
    public static function sanitize(mixed $value): array { return Service::sanitize($value); }
    public static function save(int $postId,array $data): void { Service::save($postId,$data); }
    public static function get(int $postId): array { return Repository::get($postId); }
    public static function all(int $postId): array { return Repository::get($postId); }
    public static function field(int $postId,string $field,mixed $default=null): mixed { return Repository::field($postId,$field,$default); }
    public static function title(int $postId): string { return get_the_title($postId); }
    public static function excerpt(int $postId): string { return (string)Repository::field($postId,'excerpt',''); }
    public static function address(int $postId): string { return (string)Repository::field($postId,'address',''); }
    public static function video(int $postId): string { return (string)Repository::field($postId,'video',''); }
    public static function gallery(int $postId): array { return (array)Repository::field($postId,'gallery',[]); }
    public static function categories(int $postId): array { return (array)Repository::field($postId,'categories',[]); }
}
