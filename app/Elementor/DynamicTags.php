<?php

declare(strict_types=1);

namespace Fandoogh\Elementor;

defined('ABSPATH') || exit;

use Elementor\Core\DynamicTags\Manager;
use Fandoogh\Elementor\Tags\AverageRatingTag;
use Fandoogh\Elementor\Tags\CustomerName;
use Fandoogh\Elementor\Tags\CustomerProductCategories;
use Fandoogh\Elementor\Tags\CustomerAddress;
use Fandoogh\Elementor\Tags\CustomerCategories;
use Fandoogh\Elementor\Tags\CustomerDescription;
use Fandoogh\Elementor\Tags\CustomerGallery;
use Fandoogh\Elementor\Tags\CustomerImage;
use Fandoogh\Elementor\Tags\CustomerVideo;
use Fandoogh\Elementor\Tags\ProjectMetaTag;
use Fandoogh\Elementor\Tags\DescriptionTag;
use Fandoogh\Elementor\Tags\FaqTag;
use Fandoogh\Elementor\Tags\ProductFaqTag;
use Fandoogh\Elementor\Tags\ProductReasonTag;
use Fandoogh\Elementor\Tags\ReviewCountTag;
use Fandoogh\Elementor\Tags\ReviewListTag;
use Fandoogh\Elementor\Tags\VideoGalleryTag;
use Fandoogh\Elementor\Tags\VideoPosterTag;
use Fandoogh\Elementor\Tags\VideoUrlTag;
use Fandoogh\Managers\ModuleManager;

final class DynamicTags
{
    public function __construct(
        private readonly ModuleManager $modules
    ) {
    }

    public function boot(): void
    {
        add_action(
            Support::dynamicTagsHook(),
            [$this, 'register']
        );
    }

    public function register(Manager $manager): void
    {
        /*
        |--------------------------------------------------------------------------
        | Register Group
        |--------------------------------------------------------------------------
        */

        $manager->register_group(
            'fa',
            [
                'title' => __('Fandoogh', 'fandoogh'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Register Tags
        |--------------------------------------------------------------------------
        */

        $tags = [];

        if ($this->modules->enabled('description')) {
            $tags[] = DescriptionTag::class;
        }

        if ($this->modules->enabled('video')) {
            $tags[] = VideoUrlTag::class;
            $tags[] = VideoPosterTag::class;
            $tags[] = VideoGalleryTag::class;
        }

        if ($this->modules->enabled('faq')) {
            $tags[] = FaqTag::class;
        }

        if ($this->modules->enabled('product_faq')) {
            $tags[] = ProductFaqTag::class;
        }

        if ($this->modules->enabled('product_reason')) {
            $tags[] = ProductReasonTag::class;
        }

        if ($this->modules->enabled('reviews')) {
            $tags[] = ReviewCountTag::class;
            $tags[] = AverageRatingTag::class;
            $tags[] = ReviewListTag::class;
        }

        if ($this->modules->enabled('customers')) {
            $tags[] = CustomerName::class;
            $tags[] = CustomerImage::class;
            $tags[] = CustomerDescription::class;
            $tags[] = CustomerAddress::class;
            $tags[] = CustomerProductCategories::class;
            $tags[] = CustomerCategories::class;
            $tags[] = CustomerVideo::class;
            $tags[] = CustomerGallery::class;
        }

        if ($this->modules->enabled('projects')) {
            $tags[] = ProjectMetaTag::class;
        }

        foreach ($tags as $tag) {
            $manager->register(
                new $tag()
            );
        }
    }
}
