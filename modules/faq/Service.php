<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

final class Service
{
    public static function sanitize(array $items): array
    {
        $clean = [];
        $seen = [];

        foreach ($items as $item) {
            $question = trim((string) preg_replace(
                '/\s+/u',
                ' ',
                sanitize_text_field((string) ($item['question'] ?? ''))
            ));
            $answer = wp_kses_post((string) ($item['answer'] ?? ''));
            $key = function_exists('mb_strtolower')
                ? mb_strtolower($question)
                : strtolower($question);

            if (
                $question === ''
                || trim(wp_strip_all_tags($answer, true)) === ''
                || isset($seen[$key])
            ) {
                continue;
            }

            $seen[$key] = true;
            $clean[] = ['question' => $question, 'answer' => $answer];
        }

        return $clean;
    }

    public static function save(int $termId, array $items): void
    {
        Repository::save($termId, self::sanitize($items));
    }
}
