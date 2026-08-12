<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class QuoteService
{
    public static function calculate(int $productId, int $variationId, mixed $meters, array $selectedOptionalFees = []): array|\WP_Error
    {
        if (! Catalog::isAllowed($productId)) {
            return new \WP_Error('fa_calculator_invalid_product', 'محصول انتخاب‌شده معتبر نیست.');
        }

        $meters = (float) wc_format_decimal($meters);
        $maximum = (float) apply_filters('fa_calculator_maximum_meters', 100000);

        if ($meters <= 0 || $meters > $maximum) {
            return new \WP_Error('fa_calculator_invalid_meters', 'متراژ واردشده معتبر نیست.');
        }

        $product = wc_get_product($productId);
        if (! $product instanceof \WC_Product) {
            return new \WP_Error('fa_calculator_missing_product', 'محصول انتخاب‌شده پیدا نشد.');
        }

        $cartProduct = $product;
        $variationAttributes = [];

        if ($product instanceof \WC_Product_Variable) {
            $variation = wc_get_product($variationId);
            if (
                ! $variation instanceof \WC_Product_Variation
                || $variation->get_parent_id() !== $productId
                || ! $variation->is_purchasable()
                || ! $variation->is_in_stock()
            ) {
                return new \WP_Error('fa_calculator_invalid_variation', 'ترکیب انتخاب‌شده موجود یا قابل خرید نیست.');
            }

            $cartProduct = $variation;
            $variationAttributes = $variation->get_variation_attributes();
        } elseif (! $product->is_purchasable() || ! $product->is_in_stock()) {
            return new \WP_Error('fa_calculator_unavailable_product', 'محصول انتخاب‌شده در حال حاضر قابل خرید نیست.');
        }

        $basePrice = Currency::toToman((float) $cartProduct->get_price());
        $allFees = FixedPriceService::activeForProduct($productId);
        $appliedFees = [];
        $perMeterFees = 0.0;
        $lumpSumFees = 0.0;

        foreach ($allFees as $fee) {
            $mode = $fee['mode'] ?? 'mandatory';
            if ($mode === 'optional' && ! in_array($fee['id'], $selectedOptionalFees, true)) {
                continue;
            }

            $appliedFees[] = $fee;
            if ($fee['type'] === FixedPriceService::PER_METER) {
                $perMeterFees += (float) $fee['price'];
            } else {
                $lumpSumFees += (float) $fee['price'];
            }
        }

        $pricePerMeter = $basePrice + $perMeterFees;
        $total = ($pricePerMeter * $meters) + $lumpSumFees;

        return [
            'product_id' => $productId,
            'variation_id' => $variationId,
            'variation_attributes' => $variationAttributes,
            'meters' => $meters,
            'base_price' => $basePrice,
            'price_per_meter' => $pricePerMeter,
            'total' => $total,
            'fixed_prices' => $appliedFees,
        ];
    }
}
