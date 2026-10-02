<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
defined('ABSPATH') || exit;

final class Relations
{
    public function boot(): void
    {
        add_filter('woocommerce_product_tabs', [$this, 'tabs']);
        add_shortcode('fa_related_projects', [$this, 'shortcode']);
        add_shortcode('fa_project_relations', static fn ($atts = []): string => self::details(absint($atts['id'] ?? get_the_ID())));
        add_filter('the_content', [$this, 'content']);
        add_action('before_delete_post', [$this, 'deleted']);
        foreach (['added_post_meta', 'updated_post_meta'] as $hook) add_action($hook, [$this, 'metaUpdated'], 10, 4);
        add_action('deleted_post_meta', [$this, 'metaDeleted'], 10, 4);
    }

    public function metaUpdated($metaId, int $id, string $key, $value): void
    {
        if ($key === 'fa_project' && Repository::isProject($id)) Repository::syncRelations($id, Service::sanitize($value));
    }

    public function metaDeleted($metaIds, int $id, string $key, $value): void
    {
        if ($key === 'fa_project') {
            delete_post_meta($id, '_fa_project_product');
            delete_post_meta($id, '_fa_project_customer');
        }
    }

    public static function fields(int $id): void
    {
        $data = Repository::get($id);
        foreach (['fa_customer' => ['مشتری پروژه', 'fa_project_customer', false], 'product' => ['محصولات استفاده‌شده', 'fa_project_products[]', true]] as $type => [$label, $name, $multiple]) {
            echo '<tr><th><label for="' . esc_attr($type . '-relations') . '">' . esc_html($label) . '</label></th><td>';
            echo '<select style="min-width:280px;max-width:100%" id="' . esc_attr($type . '-relations') . '" name="' . esc_attr($name) . '"' . ($multiple ? ' multiple size="8"' : '') . '>';
            if (!$multiple) echo '<option value="0">بدون مشتری</option>';
            $posts = get_posts(['post_type' => $type, 'post_status' => ['publish', 'draft', 'pending', 'private'], 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
            foreach ($posts as $post) {
                if (!current_user_can('edit_post', $post->ID)) continue;
                $selected = $multiple ? in_array($post->ID, (array) ($data['product_ids'] ?? []), true) : $post->ID === (int) ($data['customer_id'] ?? 0);
                echo '<option value="' . esc_attr((string) $post->ID) . '" ' . selected($selected, true, false) . '>' . esc_html($post->post_title . ' (#' . $post->ID . ')') . '</option>';
            }
            echo '</select>';
            if ($multiple) echo '<p class="description">برای انتخاب چند محصول کلید Ctrl یا Command را نگه دارید.</p>';
            echo '</td></tr>';
        }
    }

    public static function projects(int $id, string $type = 'product'): array
    {
        if ($id < 1) return [];
        return get_posts(['post_type' => 'fa_project', 'post_status' => 'publish', 'numberposts' => 12, 'meta_key' => $type === 'customer' ? '_fa_project_customer' : '_fa_project_product', 'meta_value' => $id, 'orderby' => 'date', 'order' => 'DESC']);
    }

    public function tabs(array $tabs): array
    {
        if (self::projects((int) get_the_ID()) !== []) {
            $tabs['fa_projects'] = ['title' => __('پروژه‌های مرتبط', 'fandoogh'), 'priority' => 45, 'callback' => function (): void { echo $this->shortcode(['product_id' => get_the_ID()]); }];
        }
        return $tabs;
    }

    public function shortcode($atts = []): string
    {
        $atts = shortcode_atts(['product_id' => 0, 'customer_id' => 0], (array) $atts);
        $customer = absint($atts['customer_id']);
        $id = $customer ?: (absint($atts['product_id']) ?: (int) get_the_ID());
        $projects = self::projects($id, $customer ? 'customer' : 'product');
        if ($projects === []) return '';
        $html = '<section class="fa-related-projects"><h2>پروژه‌های مرتبط</h2><ul>';
        foreach ($projects as $project) $html .= '<li><a href="' . esc_url(get_permalink($project->ID)) . '">' . esc_html(get_the_title($project->ID)) . '</a></li>';
        return $html . '</ul></section>';
    }

    public static function details(int $id): string
    {
        if (!Repository::isProject($id)) return '';
        $data = Repository::get($id);
        $html = '';
        foreach (array_merge([absint($data['customer_id'] ?? 0)], (array) ($data['product_ids'] ?? [])) as $related) {
            if (!$related || get_post_status($related) !== 'publish' || !is_post_publicly_viewable($related)) continue;
            $html .= '<li><a href="' . esc_url(get_permalink($related)) . '">' . esc_html(get_the_title($related)) . '</a></li>';
        }
        return $html === '' ? '' : '<section class="fa-project-relations"><h2>مشتری و محصولات پروژه</h2><ul>' . $html . '</ul></section>';
    }

    public function content(string $content): string
    {
        if (!is_main_query() || !in_the_loop()) return $content;
        if (is_singular('fa_project')) return $content . self::details((int) get_the_ID());
        if (is_singular('fa_customer')) return $content . $this->shortcode(['customer_id' => get_the_ID()]);
        return $content;
    }

    public function deleted(int $id): void
    {
        $type = get_post_type($id);
        if (!in_array($type, ['product', 'fa_customer'], true)) return;
        $projects = get_posts(['post_type' => 'fa_project', 'post_status' => ['publish', 'draft', 'pending', 'private', 'future', 'trash'], 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => $type === 'product' ? '_fa_project_product' : '_fa_project_customer', 'meta_value' => $id]);
        foreach ($projects as $project) {
            $data = Repository::get((int) $project);
            if ($type === 'product') $data['product_ids'] = array_values(array_diff((array) ($data['product_ids'] ?? []), [$id]));
            else $data['customer_id'] = 0;
            Service::save((int) $project, $data);
        }
    }
}
