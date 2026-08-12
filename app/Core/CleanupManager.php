<?php

declare(strict_types=1);

namespace Fandoogh\Core;

use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Taxonomies;
use Fandoogh\Core\Managers\CustomerManager;
use Fandoogh\Core\Managers\ProjectManager;
use Fandoogh\Core\Managers\ReviewProxyManager;
use Fandoogh\Modules\Reviews\Cache;
use Fandoogh\Modules\Video\Repository as VideoRepository;
use Fandoogh\Modules\Video\Service as VideoService;

defined('ABSPATH') || exit;

final class CleanupManager
{
    public function boot(): void
    {
        add_action('pre_delete_term', [$this, 'beforeDeleteTerm'], 10, 2);
        add_action('delete_attachment', [$this, 'deleteAttachment'], 10, 2);
    }

    public function beforeDeleteTerm(int $termId, string $taxonomy): void
    {
        if ($taxonomy !== Taxonomies::PRODUCT_CATEGORY) return;
        (new Cache())->forget($termId);
        ReviewProxyManager::delete($termId);
        $this->removeProductCategory(ContentTypes::CUSTOMER, $termId);
        $this->removeProductCategory(ContentTypes::PROJECT, $termId);
    }

    public function deleteAttachment(int $attachmentId, \WP_Post $attachment): void
    {
        $url = (string) wp_get_attachment_url($attachmentId);
        $this->removeAttachmentFromPosts(ContentTypes::CUSTOMER, $attachmentId, $url);
        $this->removeAttachmentFromPosts(ContentTypes::PROJECT, $attachmentId, $url);
        $this->removeAttachmentFromTerms($attachmentId, $url);
    }

    private function removeProductCategory(string $postType, int $termId): void
    {
        foreach ($this->postIds($postType) as $postId) {
            $manager = $postType === ContentTypes::CUSTOMER ? CustomerManager::class : ProjectManager::class;
            $data = $manager::all($postId);
            $current = array_map('absint', $manager::categories($postId));
            $categories = array_values(array_diff($current, [$termId]));
            if ($categories === $current) continue;
            $data['categories'] = $categories;
            $manager::save($postId, $data);
        }
    }

    private function removeAttachmentFromPosts(string $postType, int $attachmentId, string $url): void
    {
        foreach ($this->postIds($postType) as $postId) {
            $manager = $postType === ContentTypes::CUSTOMER ? CustomerManager::class : ProjectManager::class;
            $data = $manager::all($postId);
            $gallery = array_map('absint', $manager::gallery($postId));
            $cleanGallery = array_values(array_diff($gallery, [$attachmentId]));
            $changed = $cleanGallery !== $gallery;
            if ($changed) $data['gallery'] = $cleanGallery;
            if ($url !== '' && $manager::video($postId) === $url) {
                $data['video'] = '';
                $changed = true;
            }
            if ($changed) $manager::save($postId, $data);
        }
    }

    private function removeAttachmentFromTerms(int $attachmentId, string $url): void
    {
        if (! taxonomy_exists(Taxonomies::PRODUCT_CATEGORY)) return;
        $offset = 0;
        do {
            $termIds = get_terms(['taxonomy'=>Taxonomies::PRODUCT_CATEGORY,'hide_empty'=>false,'fields'=>'ids','number'=>100,'offset'=>$offset]);
            if (is_wp_error($termIds)) return;
            foreach ($termIds as $termId) {
                $termId = absint($termId);
                $gallery = array_map('absint', VideoRepository::gallery($termId));
                $cleanGallery = array_values(array_diff($gallery, [$attachmentId]));
                $data = [];
                if ($cleanGallery !== $gallery) $data['gallery'] = $cleanGallery;
                if (VideoRepository::poster($termId) === $attachmentId) $data['poster'] = 0;
                if ($url !== '' && VideoRepository::url($termId) === $url) $data['url'] = '';
                if ($data !== []) VideoService::save($termId, $data);
            }
            $offset += count($termIds);
        } while (count($termIds) === 100);
    }

    /** @return array<int, int> */
    private function postIds(string $postType): array
    {
        global $wpdb;
        $ids = [];
        $lastId = 0;
        do {
            $batch = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND ID > %d ORDER BY ID ASC LIMIT 100", $postType, $lastId));
            $batch = array_map('absint', $batch);
            $ids = array_merge($ids, $batch);
            if ($batch !== []) $lastId = max($batch);
        } while (count($batch) === 100);
        return $ids;
    }
}
