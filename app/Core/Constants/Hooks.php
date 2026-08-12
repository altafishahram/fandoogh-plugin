<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants;

defined('ABSPATH') || exit;

/**
 * Fandoogh Hooks
 *
 * Central registry for all custom actions and filters.
 *
 * @package Fandoogh\Core\Constants
 */
final class Hooks
{
    private function __construct()
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Description Module
     */
    public const DESCRIPTION = 'fandoogh_description';

    /**
     * Video Module
     */
    public const VIDEO = 'fandoogh_video';

    /**
     * FAQ Module
     */
    public const FAQ = 'fandoogh_faq';

    /**
     * Reviews Module
     */
    public const REVIEWS = 'fandoogh_reviews';

    /**
     * Dynamic Tags
     */
    public const DYNAMIC_TAGS = 'fandoogh_dynamic_tags';

    /**
     * Widgets
     */
    public const WIDGETS = 'fandoogh_widgets';

    /**
     * Meta Tags
     */
    public const META_TAGS = 'fandoogh_meta_tags';

    /**
     * Assets
     */
    public const ENQUEUE_ASSETS = 'fandoogh_enqueue_assets';

    /**
     * Plugin Boot
     */
    public const BOOT = 'fandoogh_boot';

    /**
     * Plugin Loaded
     */
    public const LOADED = 'fandoogh_loaded';

    /**
     * Before Render
     */
    public const BEFORE_RENDER = 'fandoogh_before_render';

    /**
     * After Render
     */
    public const AFTER_RENDER = 'fandoogh_after_render';

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Filter Description
     */
    public const FILTER_DESCRIPTION = 'fandoogh_filter_description';

    /**
     * Filter Video
     */
    public const FILTER_VIDEO = 'fandoogh_filter_video';

    /**
     * Filter FAQ
     */
    public const FILTER_FAQ = 'fandoogh_filter_faq';

    /**
     * Filter Reviews
     */
    public const FILTER_REVIEWS = 'fandoogh_filter_reviews';

    /**
     * Filter Meta
     */
    public const FILTER_META = 'fandoogh_filter_meta';

    /**
     * Filter Schema
     */
    public const FILTER_SCHEMA = 'fandoogh_filter_schema';

    public const FAQ_SCHEMA_ENABLED = 'fandoogh_faq_schema_enabled';

    public const PRODUCT_FAQ_SCHEMA_ENABLED = 'fa_product_faq_schema_enabled';

    public const PRODUCT_REASON_SCHEMA_PROPERTY = 'fa_product_reason_schema_property';

    /**
     * Filter Assets
     */
    public const FILTER_ASSETS = 'fandoogh_filter_assets';
}
