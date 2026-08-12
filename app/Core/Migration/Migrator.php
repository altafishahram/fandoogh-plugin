<?php

declare(strict_types=1);

namespace Fandoogh\Core\Migration;

use Fandoogh\Core\Constants\Options;
use Fandoogh\Core\Migration\Migrations\NormalizeStoredData;

defined('ABSPATH') || exit;

final class Migrator
{
    private const LOCK_TTL = 300;

    /** @var array<class-string<Migration>> */
    private const MIGRATIONS = [
        NormalizeStoredData::class,
    ];

    public function boot(): void
    {
        add_action('init', [$this, 'run'], 99);
    }

    public function run(): void
    {
        $installed = (string) get_option(Options::DATABASE_VERSION, '0.0.0');
        $pending = $this->pending($installed);

        if ($pending === [] || ! $this->acquireLock()) {
            return;
        }

        try {
            foreach ($pending as $migration) {
                $migration->up();
                update_option(Options::DATABASE_VERSION, $migration->version(), false);
            }

            update_option(Options::FRAMEWORK_VERSION, FA_VERSION, false);
        } catch (\Throwable $exception) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Fandoogh migration failed: ' . $exception->getMessage());
            }
        } finally {
            delete_option(Options::MIGRATION_LOCK);
        }
    }

    /** @return array<int, Migration> */
    private function pending(string $installed): array
    {
        $pending = [];

        foreach (self::MIGRATIONS as $className) {
            $migration = new $className();

            if (version_compare($migration->version(), $installed, '>')) {
                $pending[] = $migration;
            }
        }

        usort($pending, static fn (Migration $a, Migration $b): int => version_compare($a->version(), $b->version()));
        return $pending;
    }

    private function acquireLock(): bool
    {
        $now = time();
        $lock = (int) get_option(Options::MIGRATION_LOCK, 0);

        if ($lock > 0 && ($now - $lock) < self::LOCK_TTL) {
            return false;
        }

        if ($lock > 0) {
            delete_option(Options::MIGRATION_LOCK);
        }

        return add_option(Options::MIGRATION_LOCK, $now, '', false);
    }
}
