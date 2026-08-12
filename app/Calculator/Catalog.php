<?php

declare(strict_types=1);

namespace Fandoogh\Calculator;

defined('ABSPATH') || exit;

final class Catalog
{
    /** @var array<int,\WC_Product>|null */
    private static ?array $products = null;

    /** @return array<int,\WC_Product> */
    public static function products(): array
    {
        if (self::$products !== null) {
            return self::$products;
        }

        $ids = FixedPriceService::mappedProductIds(false);
        $settings = SettingsService::get();
        $allowedCategories = $settings['allowed_categories'] ?? [];

        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'no_found_rows' => true,
        ];

        if (!empty($allowedCategories)) {
            $args['tax_query'] = [[
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $allowedCategories,
            ]];
        }

        $categoryIds = get_posts($args);
        $ids = array_merge($ids, array_map('absint', $categoryIds));

        $products = [];
        foreach (array_values(array_unique($ids)) as $id) {
            $product = wc_get_product($id);
            if ($product instanceof \WC_Product && $product->is_visible()) {
                $products[$id] = $product;
            }
        }

        uasort(
            $products,
            static fn(\WC_Product $a, \WC_Product $b): int => strnatcasecmp($a->get_name(), $b->get_name())
        );

        self::$products = $products;

        return self::$products;
    }

    public static function isAllowed(int $productId): bool
    {
        return isset(self::products()[$productId]);
    }

    public static function productData(int $productId): array|\WP_Error
    {
        if (! self::isAllowed($productId)) {
            return new \WP_Error('fa_calculator_invalid_product', 'محصول انتخاب‌شده در ماشین حساب در دسترس نیست.');
        }

        $product = wc_get_product($productId);
        if (! $product instanceof \WC_Product) {
            return new \WP_Error('fa_calculator_missing_product', 'محصول انتخاب‌شده پیدا نشد.');
        }

        $attributes = [];
        $variations = [];

        if ($product instanceof \WC_Product_Variable) {
            $attributes = self::attributes($product);
            foreach ($product->get_children() as $variationId) {
                $variation = wc_get_product($variationId);
                if (
                    ! $variation instanceof \WC_Product_Variation
                    || ! $variation->is_purchasable()
                    || ! $variation->is_in_stock()
                    || ! $variation->variation_is_visible()
                ) {
                    continue;
                }

                $variations[] = [
                    'id' => $variation->get_id(),
                    'attributes' => $variation->get_variation_attributes(),
                    'price' => Currency::toToman((float) $variation->get_price()),
                ];
            }
        } elseif ($product->is_purchasable() && $product->is_in_stock()) {
            $variations[] = [
                'id' => 0,
                'attributes' => [],
                'price' => Currency::toToman((float) $product->get_price()),
            ];
        }

        return [
            'product' => [
                'id' => $productId,
                'name' => $product->get_name(),
                'type' => $product->get_type(),
            ],
            'attributes' => $attributes,
            'variations' => $variations,
            'fixed_prices' => array_map(
                static fn(array $item): array => [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'type' => $item['type'],
                    'mode' => $item['mode'] ?? 'mandatory',
                ],
                FixedPriceService::activeForProduct($productId)
            ),
        ];
    }

    private static function attributes(\WC_Product_Variable $product): array
    {
        $result = [];

        foreach ($product->get_variation_attributes() as $name => $options) {
            $key = wc_variation_attribute_name($name);
            $items = [];

            foreach ($options as $option) {
                $value = taxonomy_exists($name) ? (string) $option : sanitize_title((string) $option);
                $label = (string) $option;

                if (taxonomy_exists($name)) {
                    $term = get_term_by('slug', (string) $option, $name);
                    if ($term instanceof \WP_Term) {
                        $label = $term->name;
                    }
                }

                $items[] = ['value' => $value, 'label' => $label];
            }

            $result[] = [
                'key' => $key,
                'label' => wc_attribute_label($name, $product),
                'options' => $items,
            ];
        }

        return $result;
    }
}
