<?php

declare(strict_types=1);

namespace Fandoogh\Core\Constants;

defined('ABSPATH') || exit;

/**
 * Fandoogh Shortcodes
 *
 * Central registry for all plugin shortcodes.
 *
 * @package Fandoogh\Core\Constants
 */
final class Shortcodes
{
    private function __construct()
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Content Modules
    |--------------------------------------------------------------------------
    */

    /**
     * Description
     */
    public const DESCRIPTION = 'fa_description';

    /**
     * Video
     */
    public const VIDEO = 'fa_video';

    /**
     * Gallery
     */
    public const GALLERY = 'fa_gallery';

    /**
     * FAQ
     */
    public const FAQ = 'fa_faq';

    /** Product FAQ */
    public const PRODUCT_FAQ = 'fa_product_faq';

    /** Product purchase reason */
    public const PRODUCT_REASON = 'fa_product_reason';

    /** Fandoogh price calculator */
    public const FANDOOGH_CALCULATOR = 'fandoogh_calculator';

    /**
     * Reviews
     */
    public const REVIEWS = 'fa_reviews';

    public const CUSTOMER_NAME = 'fa_customer_name';
    public const CUSTOMER_IMAGE = 'fa_customer_image';
    public const CUSTOMER_DESCRIPTION = 'fa_customer_description';
    public const CUSTOMER_ADDRESS = 'fa_customer_address';
    public const CUSTOMER_PRODUCT_CATEGORIES = 'fa_customer_product_categories';
    public const CUSTOMER_CATEGORIES = 'fa_customer_categories';
    public const CUSTOMER_VIDEO = 'fa_customer_video';
    public const CUSTOMER_GALLERY = 'fa_customer_gallery';

    public const PROJECT_NAME = 'fa_project_name';
    public const PROJECT_IMAGE = 'fa_project_image';
    public const PROJECT_CONTRACTOR = 'fa_project_contractor';
    public const PROJECT_DESCRIPTION = 'fa_project_description';
    public const PROJECT_ADDRESS = 'fa_project_address';
    public const PROJECT_PRODUCT_CATEGORIES = 'fa_project_product_categories';
    public const PROJECT_CATEGORIES = 'fa_project_categories';
    public const PROJECT_VIDEO = 'fa_project_video';
    public const PROJECT_GALLERY = 'fa_project_gallery';

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    /**
     * Meta Title
     */
    public const META_TITLE = 'fa_meta_title';

    /**
     * Meta Description
     */
    public const META_DESCRIPTION = 'fa_meta_description';

    /*
    |--------------------------------------------------------------------------
    | Dynamic
    |--------------------------------------------------------------------------
    */

    /**
     * Dynamic Field
     */
    public const FIELD = 'fa_field';
}
