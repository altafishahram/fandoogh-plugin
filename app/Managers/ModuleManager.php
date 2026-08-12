<?php

declare(strict_types=1);

namespace Fandoogh\Managers;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

use Fandoogh\Core\Config;

final class ModuleManager
{
    /**
     * Option Key
     */
    private const OPTION_KEY = Options::MODULES;


    /**
     * لیست ماژول‌ها
     */
    private array $modules = [];


    /**
     * بارگذاری ماژول‌ها
     */
    public function boot(): void
    {
        $saved = get_option(
            self::OPTION_KEY,
            null
        );


        $defaults = Config::load(
            'modules'
        );


        /*
        |--------------------------------------------------------------------------
        | First Install
        |--------------------------------------------------------------------------
        */

        if ($saved === null) {

            $this->modules = is_array($defaults)
                ? $defaults
                : [];

            $this->save();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Existing Settings
        |--------------------------------------------------------------------------
        */

        $this->modules = is_array($saved)
            ? $saved
            : [];


        /*
        |--------------------------------------------------------------------------
        | Sync New Modules
        |--------------------------------------------------------------------------
        */

        if (is_array($defaults)) {

            foreach ($defaults as $key => $value) {

                if (! array_key_exists($key, $this->modules)) {

                    $this->modules[$key] = $value;

                }

            }

        }


        $this->save();
    }


    /**
     * همه ماژول‌ها
     */
    public function all(): array
    {
        return $this->modules;
    }


    /**
     * بررسی فعال بودن ماژول
     */
    public function enabled(string $module): bool
    {
        return ! empty(
            $this->modules[$module]
        );
    }


    /**
     * فعال کردن ماژول
     */
    public function enable(string $module): void
    {
        $this->modules[$module] = true;

        $this->save();
    }


    /**
     * غیرفعال کردن ماژول
     */
    public function disable(string $module): void
    {
        $this->modules[$module] = false;

        $this->save();
    }


    /**
     * تغییر وضعیت
     */
    public function toggle(string $module): void
    {
        $this->modules[$module] = ! $this->enabled(
            $module
        );

        $this->save();
    }


    /**
     * ذخیره تنظیمات
     */
    private function save(): void
    {
        update_option(
            self::OPTION_KEY,
            $this->modules
        );
    }


    /**
     * Module registry.
     */
    public function registry(): array
    {
        $registry = Config::load(
            'module-registry'
        );

        return is_array($registry)
            ? $registry
            : [];
    }
}
