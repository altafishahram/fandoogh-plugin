<?php

declare(strict_types=1);

namespace Fandoogh\Core;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class Deactivator
{
    /**
     * Run plugin deactivation tasks.
     */
    public static function deactivate(): void
    {
        delete_option(Options::MIGRATION_LOCK);
        flush_rewrite_rules(false);
    }
}
