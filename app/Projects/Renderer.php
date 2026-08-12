<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
use Fandoogh\Core\Constants\Taxonomies;
use Fandoogh\Core\Managers\ProjectManager;
defined('ABSPATH') || exit;
final class Renderer
{
    public static function name(int $id): string{return esc_html(ProjectManager::title($id));}
    public static function image(int $id): string{return (string)get_the_post_thumbnail($id,'large',['class'=>'fa-project-image']);}
    public static function contractor(int $id): string{return esc_html(ProjectManager::contractor($id));}
    public static function description(int $id): string{return wpautop(wp_kses_post(ProjectManager::excerpt($id)));}
    public static function address(int $id): string{return esc_html(ProjectManager::address($id));}
    public static function video(int $id): string{$url=ProjectManager::video($id);return $url===''?'':sprintf('<video class="fa-project-video" controls preload="metadata"><source src="%s"></video>',esc_url($url));}
    public static function gallery(int $id): string{$html='';foreach(ProjectManager::gallery($id) as $imageId){$html.=wp_get_attachment_image((int)$imageId,'medium',false,['class'=>'fa-project-gallery-image']);}return $html===''?'':'<div class="fa-project-gallery">'.$html.'</div>';}
    public static function productCategories(int $id): string{return self::terms($id,Taxonomies::PRODUCT_CATEGORY,ProjectManager::categories($id));}
    public static function categories(int $id): string{return self::terms($id,Taxonomy::NAME,[]);}
    private static function terms(int $id,string $taxonomy,array $ids): string{$terms=$ids?array_map(static fn($termId)=>get_term((int)$termId,$taxonomy),$ids):get_the_terms($id,$taxonomy);if(!is_array($terms))return '';return esc_html(implode('، ',array_values(array_filter(array_map(static fn($term)=>$term instanceof \WP_Term?$term->name:'',$terms)))));}
}
