<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class Currency
{
    public static function toToman(float $storeAmount): float
    {
        return self::usesRial() ? $storeAmount / 10 : $storeAmount;
    }

    public static function fromToman(float $tomanAmount): float
    {
        return self::usesRial() ? $tomanAmount * 10 : $tomanAmount;
    }

    private static function usesRial(): bool
    {
        $currency = function_exists('get_woocommerce_currency')
            ? strtoupper((string) get_woocommerce_currency())
            : 'IRT';

        return in_array($currency, ['IRR', 'RIAL'], true);
    }
}
