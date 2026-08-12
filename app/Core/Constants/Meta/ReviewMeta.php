<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants\Meta;

defined('ABSPATH') || exit;

/**
 * Review Meta Keys.
 *
 * @package Fandoogh\Core\Constants\Meta
 */
final class ReviewMeta
{
    public const PROXY_ID = 'fa_review_proxy_id';

    public const PROXY_OBJECT_TYPE = 'fa_review_object_type';

    public const PROXY_OBJECT_ID = 'fa_review_object_id';

    /**
     * Review rating.
     */
    public const RATING = 'fa_rating';

    /**
     * Related object type.
     */
    public const OBJECT_TYPE = 'fa_object_type';

    /**
     * Related object ID.
     */
    public const OBJECT_ID = 'fa_object_id';
}
