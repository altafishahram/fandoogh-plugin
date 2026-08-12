<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Video;
defined('ABSPATH') || exit;
final class Renderer
{
    public static function render(int $termId,array $args=[]):string{$data=Video::get($termId);$url=(string)$data['url'];if($url==='')return '';$args=wp_parse_args($args,['controls'=>true,'autoplay'=>false]);$poster=(int)$data['poster']>0?wp_get_attachment_image_url((int)$data['poster'],'full'):false;$attributes=$args['controls']?' controls':'';$attributes.=$args['autoplay']?' autoplay muted playsinline':'';$posterAttr=$poster?' poster="'.esc_url($poster).'"':'';return '<video class="fa-video" width="100%" preload="metadata"'.$posterAttr.$attributes.'><source src="'.esc_url($url).'" type="video/mp4"></video>';}
}
