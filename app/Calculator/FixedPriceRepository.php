<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class FixedPriceRepository
{
    public static function all(): array
    {
        $items = get_option(Options::CALCULATOR_FIXED_PRICES, []);

        return is_array($items) ? $items : [];
    }

    public static function save(array $items): bool
    {
        return update_option(Options::CALCULATOR_FIXED_PRICES, $items, false);
    }
}
