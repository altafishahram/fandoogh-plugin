<?php

declare(strict_types=1);

namespace Fandoogh\Modules\Reviews;

use Fandoogh\Core\Managers\ReviewProxyManager;

defined('ABSPATH') || exit;

/**
 * Reviews Sync.
 *
 * Synchronizes review data with
 * WooCommerce product categories.
 *
 * @package Fandoogh\Modules\Reviews
 */
final class Sync
{
    /**
     * Boot synchronization hooks.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action(
            'created_product_cat',
            [$this, 'createProxy']
        );

    }

    /**
     * Create proxy post for category.
     *
     * @param int $termId
     *
     * @return void
     */
    public function createProxy(
        int $termId
    ): void {

        ReviewProxyManager::create(
            $termId
        );
    }

}
