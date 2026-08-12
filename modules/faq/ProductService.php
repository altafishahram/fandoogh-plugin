<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

defined('ABSPATH') || exit;

final class ProductService
{
    public const DEFAULT_REASON_QUESTION = 'چرا این محصول را بخریم؟';

    public static function faq(int $productId): array
    {
        return Service::sanitize(ProductRepository::faq($productId));
    }

    public static function saveFaq(int $productId, array $items): void
    {
        ProductRepository::saveFaq($productId, Service::sanitize($items));
    }

    /** @return array{question:string,answer:string} */
    public static function reason(int $productId): array
    {
        $reason = ProductRepository::reason($productId);

        return self::sanitizeReason($reason['question'], $reason['answer']);
    }

    public static function saveReason(int $productId, string $question, string $answer): void
    {
        $reason = self::sanitizeReason($question, $answer);
        ProductRepository::saveReason($productId, $reason['question'], $reason['answer']);
    }

    /** @return array{question:string,answer:string} */
    public static function sanitizeReason(string $question, string $answer): array
    {
        $question = trim((string) preg_replace(
            '/\s+/u',
            ' ',
            sanitize_text_field($question)
        ));
        $answer = wp_kses_post($answer);

        if (trim(wp_strip_all_tags($answer, true)) === '') {
            return ['question' => '', 'answer' => ''];
        }

        if ($question === '') {
            $question = self::DEFAULT_REASON_QUESTION;
        }

        return ['question' => $question, 'answer' => $answer];
    }

    public static function isProduct(int $postId): bool
    {
        return $postId > 0 && get_post_type($postId) === 'product';
    }
}
