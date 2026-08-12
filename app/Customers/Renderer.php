<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Constants\Taxonomies;
use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class Renderer
{
    public static function name(int $postId): string { return esc_html(CustomerManager::title($postId)); }
    public static function image(int $postId): string { return (string) get_the_post_thumbnail($postId, 'large', ['class' => 'fa-customer-image']); }
    public static function description(int $postId): string { return wpautop(wp_kses_post(CustomerManager::excerpt($postId))); }
    public static function address(int $postId): string { return esc_html(CustomerManager::address($postId)); }
    public static function video(int $postId): string { $url=CustomerManager::video($postId); return $url===''?'':sprintf('<video class="fa-customer-video" controls preload="metadata"><source src="%s"></video>',esc_url($url)); }
    public static function gallery(int $postId): string { $html=''; foreach(CustomerManager::gallery($postId) as $id){$html.=wp_get_attachment_image((int)$id,'medium',false,['class'=>'fa-customer-gallery-image']);} return $html===''?'':'<div class="fa-customer-gallery">'.$html.'</div>'; }
    public static function productCategories(int $postId): string { return self::terms($postId,Taxonomies::PRODUCT_CATEGORY,CustomerManager::categories($postId),true); }
    public static function categories(int $postId): string { return self::terms($postId,Taxonomy::NAME,[],false); }
    private static function terms(int $postId,string $taxonomy,array $ids,bool $links): string { $terms=$ids?array_map(static fn($id)=>get_term((int)$id,$taxonomy),$ids):get_the_terms($postId,$taxonomy); if(!is_array($terms))return ''; $out=[]; foreach($terms as $term){if(!$term instanceof \WP_Term)continue; $url=get_term_link($term); $out[]=$links&&!is_wp_error($url)?sprintf('<a href="%s">%s</a>',esc_url($url),esc_html($term->name)):esc_html($term->name);} return implode('، ',$out); }
}
