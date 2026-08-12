<?php

declare(strict_types=1);

namespace Fandoogh\Core\Migration\Migrations;

use Fandoogh\Core\Config;
use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\FaqMeta;
use Fandoogh\Core\Constants\Meta\ReviewMeta;
use Fandoogh\Core\Constants\Meta\VideoMeta;
use Fandoogh\Core\Constants\Options;
use Fandoogh\Core\Constants\Taxonomies;
use Fandoogh\Core\Migration\Migration;
use Fandoogh\Customers\Repository as CustomerRepository;
use Fandoogh\Customers\Service as CustomerService;
use Fandoogh\Modules\Faq\Repository as FaqRepository;
use Fandoogh\Modules\Faq\Service as FaqService;
use Fandoogh\Modules\Video\Repository as VideoRepository;
use Fandoogh\Modules\Video\Service as VideoService;
use Fandoogh\Projects\Repository as ProjectRepository;
use Fandoogh\Projects\Service as ProjectService;

defined('ABSPATH') || exit;

final class NormalizeStoredData implements Migration
{
    public function version(): string
    {
        return '1.0.0';
    }

    public function up(): void
    {
        $this->normalizeModules();
        $this->normalizePosts(ContentTypes::CUSTOMER, CustomerRepository::class, CustomerService::class);
        $this->normalizePosts(ContentTypes::PROJECT, ProjectRepository::class, ProjectService::class);
        $this->normalizeProductTerms();
        $this->normalizeRatings();
    }

    private function normalizeModules(): void
    {
        $defaults = Config::load('modules');
        $stored = get_option(Options::MODULES, []);
        $defaults = is_array($defaults) ? $defaults : [];
        $stored = is_array($stored) ? $stored : [];
        $normalized = [];

        foreach ($defaults as $key => $enabled) {
            $normalized[sanitize_key((string) $key)] = array_key_exists($key, $stored)
                ? (bool) $stored[$key]
                : (bool) $enabled;
        }

        update_option(Options::MODULES, $normalized, false);
    }

    /** @param class-string $repository @param class-string $service */
    private function normalizePosts(string $postType, string $repository, string $service): void
    {
        $page = 1;

        do {
            $query = new \WP_Query([
                'post_type' => $postType,
                'post_status' => 'any',
                'fields' => 'ids',
                'posts_per_page' => 100,
                'paged' => $page,
                'orderby' => 'ID',
                'order' => 'ASC',
                'no_found_rows' => true,
            ]);
            $ids = array_map('absint', $query->posts);

            foreach ($ids as $postId) {
                $data = $repository::get($postId);
                $repository::save($postId, array_merge($data, $service::sanitize($data)));
            }

            $page++;
        } while (count($ids) === 100);
    }

    private function normalizeProductTerms(): void
    {
        $offset = 0;

        do {
            $termIds = get_terms([
                'taxonomy' => Taxonomies::PRODUCT_CATEGORY,
                'hide_empty' => false,
                'fields' => 'ids',
                'number' => 100,
                'offset' => $offset,
                'orderby' => 'term_id',
                'order' => 'ASC',
            ]);

            if (is_wp_error($termIds)) {
                return;
            }

            foreach ($termIds as $termId) {
                $termId = absint($termId);

                if (metadata_exists('term', $termId, FaqMeta::FAQ)) {
                    FaqRepository::save($termId, FaqService::sanitize(FaqRepository::get($termId)));
                }

                $video = [];
                if (metadata_exists('term', $termId, VideoMeta::URL)) $video['url'] = VideoRepository::url($termId);
                if (metadata_exists('term', $termId, VideoMeta::POSTER)) $video['poster'] = VideoRepository::poster($termId);
                if (metadata_exists('term', $termId, VideoMeta::GALLERY)) $video['gallery'] = VideoRepository::gallery($termId);
                if ($video !== []) VideoService::save($termId, $video);
            }

            $offset += count($termIds);
        } while (count($termIds) === 100);
    }

    private function normalizeRatings(): void
    {
        $offset = 0;

        do {
            $commentIds = get_comments([
                'fields' => 'ids',
                'status' => 'all',
                'meta_key' => ReviewMeta::RATING,
                'number' => 200,
                'offset' => $offset,
                'orderby' => 'comment_ID',
                'order' => 'ASC',
            ]);

            foreach ($commentIds as $commentId) {
                $rating = (int) get_comment_meta((int) $commentId, ReviewMeta::RATING, true);
                update_comment_meta((int) $commentId, ReviewMeta::RATING, max(1, min(5, $rating)));
            }

            $offset += count($commentIds);
        } while (count($commentIds) === 200);
    }
}
