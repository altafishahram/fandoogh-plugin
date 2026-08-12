<?php

declare(strict_types=1);

namespace Fandoogh\Core;

defined('ABSPATH') || exit;

final class Container
{
    /**
     * سرویس‌های ثبت شده
     */
    private array $services = [];

    /**
     * ثبت سرویس
     */
    public function set(string $key, object $service): void
    {
        $this->services[$key] = $service;
    }
        /**
     * Check whether a service exists in the container.
     *
     * @param string $key Service identifier.
     * @return bool True if the service exists.
     */
    public function has(string $key): bool
    {
        return isset($this->services[$key]);
    }

    /**
     * دریافت سرویس
     */
    public function get(string $key): ?object
    {
        return $this->services[$key] ?? null;
    }
}