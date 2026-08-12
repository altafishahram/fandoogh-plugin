<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Fandoogh\Core\Constants\Hooks;

defined('ABSPATH') || exit;

final class ProductSchema
{
    /** @var array<int,true> */
    private static array $visibleFaq = [];

    /** @var array<int,true> */
    private static array $visibleReason = [];

    /** @var array<int,true> */
    private static array $emittedFaq = [];

    public function __construct(
        private readonly bool $reasonEnabled = true
    ) {
    }

    public function boot(): void
    {
        if ($this->reasonEnabled) {
            add_filter('woocommerce_structured_data_product', [$this, 'addReason'], 20, 2);
        }
    }

    public static function markFaqVisible(int $productId): void
    {
        if ($productId > 0) {
            self::$visibleFaq[$productId] = true;
        }
    }

    public static function markReasonVisible(int $productId): void
    {
        if ($productId > 0) {
            self::$visibleReason[$productId] = true;
        }
    }

    public static function isFaqVisible(int $productId): bool
    {
        return isset(self::$visibleFaq[$productId]);
    }

    public static function isReasonVisible(int $productId): bool
    {
        return isset(self::$visibleReason[$productId]);
    }

    public static function renderFaq(int $productId): string
    {
        if (
            ! self::isFaqVisible($productId)
            || isset(self::$emittedFaq[$productId])
            || ! apply_filters(Hooks::PRODUCT_FAQ_SCHEMA_ENABLED, true, $productId)
        ) {
            return '';
        }

        $entities = [];

        foreach (ProductService::faq($productId) as $item) {
            $answer = self::plainText((string) $item['answer']);

            if ($answer === '') {
                continue;
            }

            $entities[] = [
                '@type' => 'Question',
                'name' => (string) $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($entities === []) {
            return '';
        }

        $schema = apply_filters(
            Hooks::FILTER_SCHEMA,
            [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $entities,
            ],
            'product_faq',
            $productId
        );

        if (! is_array($schema) || $schema === []) {
            return '';
        }

        $json = wp_json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );

        if (! is_string($json) || $json === '') {
            return '';
        }

        self::$emittedFaq[$productId] = true;

        return "\n<script type=\"application/ld+json\" class=\"fa-product-faq-schema\">"
            . $json . "</script>\n";
    }

    /**
     * Add the visible purchase reason to WooCommerce's existing Product node.
     *
     * @param array<string,mixed> $markup
     * @return array<string,mixed>
     */
    public function addReason(array $markup, mixed $product): array
    {
        if (! $product instanceof \WC_Product) {
            return $markup;
        }

        $productId = $product->get_id();

        if (! self::isReasonVisible($productId)) {
            return $markup;
        }

        $reason = ProductService::reason($productId);
        $answer = self::plainText($reason['answer']);

        if ($reason['question'] === '' || $answer === '') {
            return $markup;
        }

        $property = apply_filters(
            Hooks::PRODUCT_REASON_SCHEMA_PROPERTY,
            [
                '@type' => 'PropertyValue',
                'propertyID' => 'fandoogh:purchase-reason',
                'name' => $reason['question'],
                'value' => $answer,
            ],
            $productId
        );

        if (! is_array($property) || $property === []) {
            return $markup;
        }

        $properties = self::normalizeProperties($markup['additionalProperty'] ?? []);

        foreach ($properties as $existing) {
            if (is_array($existing) && ($existing['propertyID'] ?? '') === 'fandoogh:purchase-reason') {
                return $markup;
            }
        }

        $properties[] = $property;
        $markup['additionalProperty'] = $properties;

        return $markup;
    }

    private static function plainText(string $html): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', wp_strip_all_tags($html, true)));
    }

    /** @return array<int,mixed> */
    private static function normalizeProperties(mixed $properties): array
    {
        if (! is_array($properties) || $properties === []) {
            return [];
        }

        return array_is_list($properties) ? $properties : [$properties];
    }
}
