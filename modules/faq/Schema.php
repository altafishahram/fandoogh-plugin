<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Fandoogh\Core\Constants\Hooks;

defined('ABSPATH') || exit;

/**
 * Builds FAQPage structured data for product categories.
 */
final class Schema
{
    /** @var array<int, true> */
    private static array $visibleTerms = [];

    /** @var array<int, true> */
    private static array $emittedTerms = [];

    public static function markVisible(int $termId): void
    {
        if ($termId > 0) self::$visibleTerms[$termId] = true;
    }

    public static function isVisible(int $termId): bool
    {
        return isset(self::$visibleTerms[$termId]);
    }

    /**
     * Render a safe JSON-LD script tag.
     */
    public static function render(int $termId): string
    {
        if (
            $termId <= 0
            || isset(self::$emittedTerms[$termId])
            || ! self::isVisible($termId)
            || ! apply_filters(Hooks::FAQ_SCHEMA_ENABLED, true, $termId)
        ) {
            return '';
        }

        $entities = [];

        $seen = [];

        foreach (Service::sanitize(Faq::get($termId)) as $item) {
            $question = sanitize_text_field(
                (string) ($item['question'] ?? '')
            );

            $answer = trim(
                wp_strip_all_tags(
                    (string) ($item['answer'] ?? ''),
                    true
                )
            );

            $question = trim((string) preg_replace('/\s+/u', ' ', $question));
            $answer = trim((string) preg_replace('/\s+/u', ' ', $answer));
            $dedupeKey = function_exists('mb_strtolower') ? mb_strtolower($question) : strtolower($question);

            if ($question === '' || $answer === '' || isset($seen[$dedupeKey])) {
                continue;
            }

            $seen[$dedupeKey] = true;

            $entities[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($entities === []) {
            return '';
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];

        /**
         * Filters the complete FAQPage schema before it is encoded.
         * Return an empty array to suppress Fandoogh's schema when another
         * SEO plugin already provides FAQ structured data for this archive.
         */
        $schema = apply_filters(
            Hooks::FILTER_SCHEMA,
            $schema,
            'faq',
            $termId
        );

        if (! is_array($schema) || $schema === []) {
            return '';
        }

        $json = wp_json_encode(
            $schema,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        );

        if (! is_string($json) || $json === '') {
            return '';
        }

        self::$emittedTerms[$termId] = true;

        return "\n<script type=\"application/ld+json\" class=\"fa-faq-schema\">"
            . $json
            . "</script>\n";
    }
}
