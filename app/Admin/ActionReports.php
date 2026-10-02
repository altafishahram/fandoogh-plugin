<?php
declare(strict_types=1);
namespace Fandoogh\Admin;
use Fandoogh\Core\Application;
use Fandoogh\Managers\ModuleManager;
use Fandoogh\Projects\Repository;
use Fandoogh\Modules\Faq\ProductRepository;
use Fandoogh\Modules\Video\Repository as VideoRepository;
defined('ABSPATH') || exit;

final class ActionReports
{
    public static function render(): void
    {
        if (!current_user_can('manage_options')) return;
        $modules = Application::instance()->get('modules');
        if (!$modules instanceof ModuleManager) return;
        $page = max(1, absint($_GET['fa_report_page'] ?? 1));
        $rows = [];
        $pages = 1;
        if ($modules->enabled('reviews') && current_user_can('moderate_comments')) {
            $comments = get_comments(['status' => 'hold', 'post_type' => ['product', 'fa_review_proxy'], 'number' => 50, 'offset' => ($page - 1) * 50, 'orderby' => 'comment_ID', 'order' => 'ASC']);
            $total = (int) get_comments(['status' => 'hold', 'post_type' => ['product', 'fa_review_proxy'], 'count' => true]);
            $pages = max($pages, (int) ceil($total / 50));
            foreach ($comments as $comment) $rows[] = [$comment->comment_author, 'نظر منتظر تأیید', admin_url('comment.php?action=editcomment&c=' . $comment->comment_ID)];
        }
        foreach (['product' => 'product_faq', 'fa_project' => 'projects'] as $type => $module) {
            if (!$modules->enabled($module)) continue;
            $query = new \WP_Query(['post_type' => $type, 'post_status' => ['publish', 'draft', 'pending', 'private', 'future'], 'posts_per_page' => 50, 'paged' => $page, 'orderby' => 'ID', 'order' => 'ASC']);
            $pages = max($pages, (int) $query->max_num_pages);
            foreach ($query->posts as $post) {
                if (!current_user_can('edit_post', $post->ID)) continue;
                $issues = [];
                if ($type === 'product') {
                    $faq = array_filter(ProductRepository::faq($post->ID), static fn ($item): bool => is_array($item) && trim((string) ($item['question'] ?? '')) !== '' && trim(wp_strip_all_tags((string) ($item['answer'] ?? ''))) !== '');
                    if ($faq === []) $issues[] = 'محصول بدون سؤال و پاسخ کامل';
                } else {
                    $data = Repository::get($post->ID);
                    $customer = absint($data['customer_id'] ?? 0);
                    if ($modules->enabled('customers') && (!$customer || get_post_type($customer) !== 'fa_customer' || get_post_status($customer) === 'trash')) $issues[] = 'پروژه بدون مشتری معتبر';
                    $products = array_filter((array) ($data['product_ids'] ?? []), static fn ($id): bool => get_post_type($id) === 'product' && get_post_status($id) !== 'trash');
                    if ($products === []) $issues[] = 'پروژه بدون محصول مرتبط';
                    if (trim(wp_strip_all_tags((string) ($data['excerpt'] ?? ''))) === '') $issues[] = 'پروژه بدون توضیحات';
                }
                if ($issues !== []) $rows[] = [$post->post_title ?: '(بدون عنوان)', implode('، ', $issues), get_edit_post_link($post->ID, 'raw')];
            }
        }
        if ($modules->enabled('video') && taxonomy_exists('product_cat')) {
            $count = wp_count_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
            if (!is_wp_error($count)) $pages = max($pages, (int) ceil((int) $count / 50));
            $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 50, 'offset' => ($page - 1) * 50, 'orderby' => 'term_id', 'order' => 'ASC']);
            if (!is_wp_error($terms) && current_user_can('manage_product_terms')) foreach ($terms as $term) {
                if (VideoRepository::url((int) $term->term_id) === '') $rows[] = [$term->name, 'دسته محصول بدون ویدیو', get_edit_term_link($term->term_id, 'product_cat', 'product')];
            }
        }
        echo '<section class="fa-panel"><header><h2>گزارش‌های قابل اقدام</h2></header><p>در هر صفحه حداکثر ۵۰ مورد از هر نوع محتوا بررسی می‌شود؛ فقط ماژول‌های فعال گزارش دارند.</p>';
        if ($rows === []) echo '<p>در محتوای بررسی‌شده این صفحه موردی برای رسیدگی پیدا نشد.</p>';
        else {
            echo '<table class="widefat striped"><thead><tr><th>عنوان</th><th>نیاز به رسیدگی</th><th>اقدام</th></tr></thead><tbody>';
            foreach ($rows as [$title, $issue, $url]) echo '<tr><td>' . esc_html($title) . '</td><td>' . esc_html($issue) . '</td><td><a href="' . esc_url((string) $url) . '">ویرایش و تکمیل</a></td></tr>';
            echo '</tbody></table>';
        }
        echo '<p>صفحه ' . esc_html((string) $page) . ' از ' . esc_html((string) $pages) . '</p>';
        foreach (['صفحه قبل' => $page - 1, 'صفحه بعد' => $page + 1] as $label => $target) {
            if ($target < 1 || $target > $pages) continue;
            echo '<a class="button" href="' . esc_url(add_query_arg(['page' => 'fa', 'fa_report_page' => $target], admin_url('admin.php'))) . '">' . esc_html($label) . '</a> ';
        }
        echo '</section>';
    }
}
