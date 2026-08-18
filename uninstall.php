<?php

declare(strict_types=1);

defined('WP_UNINSTALL_PLUGIN') || exit;

// Generated theme files are derived assets and are safe to remove on every uninstall.
$uploads = wp_get_upload_dir();
if (empty($uploads['error'])) {
    $themeDir = trailingslashit((string) $uploads['basedir']) . 'fandoogh/admin-theme';
    foreach ((array) glob(trailingslashit($themeDir) . 'generated-*.css') as $themeFile) {
        wp_delete_file((string) $themeFile);
    }
    if (is_dir($themeDir)) {
        @rmdir($themeDir); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
    }
}
delete_option('fa_admin_theme_asset');
delete_option('fa_admin_theme_schema_version');
delete_option('fa_admin_theme_generation_lock');

if (! (bool) get_option('fa_delete_data_on_uninstall', false)) {
    return;
}

/**
 * Delete plugin-owned posts through WordPress so comments, relationships and
 * post metadata are removed by core. Uploaded media are intentionally kept.
 */
foreach (['fa_customer', 'fa_project', 'fa_review_proxy'] as $postType) {
    do {
        $postIds = get_posts([
            'post_type' => $postType,
            'post_status' => 'any',
            'fields' => 'ids',
            'posts_per_page' => 100,
            'orderby' => 'ID',
            'order' => 'ASC',
            'no_found_rows' => true,
            'suppress_filters' => true,
        ]);

        foreach ($postIds as $postId) {
            wp_delete_post((int) $postId, true);
        }
    } while (count($postIds) === 100);
}

// Only taxonomies owned by Fandoogh are removed; WooCommerce product_cat stays.
foreach (['fa_customer_category', 'fa_project_category'] as $taxonomy) {
    if (! taxonomy_exists($taxonomy)) {
        register_taxonomy($taxonomy, [], ['public' => false]);
    }
    $termIds = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'fields' => 'ids',
    ]);
    if (! is_wp_error($termIds)) {
        foreach ($termIds as $termId) {
            wp_delete_term((int) $termId, $taxonomy);
        }
    }
}

global $wpdb;

$postMetaKeys = [
    'fa_customer', 'fa_project', 'fa_review_object_type', 'fa_review_object_id',
    'fa_meta_title', 'fa_meta_description',
    'fa_product_faq', 'fa_product_reason_question', 'fa_product_reason_answer',
];
$termMetaKeys = [
    'fa_description', 'fa_faq', 'fa_video', 'fa_video_poster', 'fa_video_gallery',
    'fa_review_proxy_id', 'fa_meta_title', 'fa_meta_description',
];
$commentMetaKeys = ['fa_rating', 'fa_object_type', 'fa_object_id'];

foreach ($postMetaKeys as $key) {
    delete_post_meta_by_key($key);
}
foreach ($termMetaKeys as $key) {
    delete_metadata('term', 0, $key, '', true);
}
foreach ($commentMetaKeys as $key) {
    delete_metadata('comment', 0, $key, '', true);
}

$options = [
    'fa_framework_version', 'fa_version', 'fa_build', 'fa_db_version',
    'fa_migration_lock', 'fa_modules', 'fa_settings', 'fa_delete_data_on_uninstall',
    'fa_cache_version', 'fa_cache_flush', 'fa_elementor', 'fa_license_key',
    'fa_license_status', 'fa_debug', 'fa_customer_rewrite_version',
    'fa_project_rewrite_version', 'fa_customer_category_children',
    'fa_project_category_children',
    'fa_admin_theme_settings', 'fa_admin_theme_asset', 'fa_admin_theme_schema_version', 'fa_admin_theme_generation_lock',
    'fa_calculator_fixed_prices',
];
foreach ($options as $option) {
    delete_option($option);
}

// Cached review aggregates have dynamic suffixes and therefore need prefix cleanup.
$transientPrefix = $wpdb->esc_like('_transient_fa_review_') . '%';
$timeoutPrefix = $wpdb->esc_like('_transient_timeout_fa_review_') . '%';
$orderStatsPrefix = $wpdb->esc_like('_transient_fa_oc_stats_') . '%';
$orderStatsTimeoutPrefix = $wpdb->esc_like('_transient_timeout_fa_oc_stats_') . '%';
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
        $transientPrefix,
        $timeoutPrefix,
        $orderStatsPrefix,
        $orderStatsTimeoutPrefix
    )
);

wp_cache_flush();
