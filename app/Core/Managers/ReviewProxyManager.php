<?php

declare(strict_types=1);

namespace Fandoogh\Core\Managers;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\ReviewMeta;
use Fandoogh\Core\Constants\Taxonomies;

defined('ABSPATH') || exit;

final class ReviewProxyManager
{
    public static function get(int $termId): int
    {
        $proxyId = (int) get_term_meta(
            $termId,
            ReviewMeta::PROXY_ID,
            true
        );

        if (
            $proxyId > 0 &&
            get_post($proxyId)
        ) {
            return $proxyId;
        }

        return self::create($termId);
    }

    public static function create(int $termId): int
    {
        $term = get_term($termId);

        if (
            !$term ||
            is_wp_error($term)
        ) {
            return 0;
        }

        $proxyId = wp_insert_post(
            [

                'post_type'   => ContentTypes::REVIEW_PROXY,

                'post_status' => 'publish',

                'post_title'  => sprintf(
                    'Review Proxy #%d - %s',
                    $termId,
                    $term->name
                ),

            ],
            true
        );

        if (
            is_wp_error($proxyId)
        )   {
            return 0;
            }
        update_post_meta(
             $proxyId,
            ReviewMeta::PROXY_OBJECT_TYPE,
            Taxonomies::PRODUCT_CATEGORY
        );

        update_post_meta(
            $proxyId,
            ReviewMeta::PROXY_OBJECT_ID,
            $termId
        );

        update_term_meta(
            $termId,
            ReviewMeta::PROXY_ID,
            $proxyId
        );

        return (int) $proxyId;
    }

    public static function delete(int $termId): bool
    {
        $proxyId = (int) get_term_meta(
            $termId,
            ReviewMeta::PROXY_ID,
            true
        );

        if ($proxyId > 0) {

            wp_delete_post(
                $proxyId,
                true
            );

        }

        delete_term_meta(
            $termId,
            ReviewMeta::PROXY_ID
        );

        return true;
    }
}
