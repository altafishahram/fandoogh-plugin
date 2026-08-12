<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Faq;

use Fandoogh\Core\Constants\Meta\ProductContentMeta;
use Fandoogh\Core\Managers\MetaManager;

defined('ABSPATH') || exit;

final class ProductRepository
{
    public static function faq(int $productId): array
    {
        $items = MetaManager::getPostMeta($productId, ProductContentMeta::FAQ, []);

        return is_array($items) ? $items : [];
    }

    public static function saveFaq(int $productId, array $items): void
    {
        if ($items === []) {
            MetaManager::deletePostMeta($productId, ProductContentMeta::FAQ);
            return;
        }

        MetaManager::updatePostMeta($productId, ProductContentMeta::FAQ, $items);
    }

    /** @return array{question:string,answer:string} */
    public static function reason(int $productId): array
    {
        return [
            'question' => (string) MetaManager::getPostMeta(
                $productId,
                ProductContentMeta::REASON_QUESTION,
                ''
            ),
            'answer' => (string) MetaManager::getPostMeta(
                $productId,
                ProductContentMeta::REASON_ANSWER,
                ''
            ),
        ];
    }

    public static function saveReason(int $productId, string $question, string $answer): void
    {
        self::saveValue($productId, ProductContentMeta::REASON_QUESTION, $question);
        self::saveValue($productId, ProductContentMeta::REASON_ANSWER, $answer);
    }

    private static function saveValue(int $productId, string $key, string $value): void
    {
        if ($value === '') {
            MetaManager::deletePostMeta($productId, $key);
            return;
        }

        MetaManager::updatePostMeta($productId, $key, $value);
    }
}
