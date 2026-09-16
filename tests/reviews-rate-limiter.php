<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . DIRECTORY_SEPARATOR);

final class WP_Error
{
    public function __construct(public string $code, public string $message)
    {
    }
}

$transients = [];

function get_transient(string $key): int
{
    global $transients;
    return $transients[$key] ?? 0;
}

function set_transient(string $key, int $value, int $expiration): bool
{
    global $transients;
    $transients[$key] = $value;
    return true;
}

function apply_filters(string $hook, int $value, int $termId): int
{
    return $value;
}

function __(string $text, string $domain): string
{
    return $text;
}

function sanitize_text_field(string $value): string
{
    return trim($value);
}

function wp_salt(string $scheme): string
{
    return 'test-salt';
}

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

require_once dirname(__DIR__) . '/modules/Reviews/RateLimiter.php';

$limiter = new \Fandoogh\Modules\Reviews\RateLimiter();

for ($attempt = 0; $attempt < 3; $attempt++) {
    if ($limiter->check(42, 'test@example.com') !== true) {
        fwrite(STDERR, "Rate limiter rejected an allowed request." . PHP_EOL);
        exit(1);
    }

    $limiter->hit(42, 'test@example.com');
}

if (! $limiter->check(42, 'test@example.com') instanceof WP_Error) {
    fwrite(STDERR, "Rate limiter did not block the fourth request." . PHP_EOL);
    exit(1);
}

echo "Reviews rate-limiter test passed." . PHP_EOL;
