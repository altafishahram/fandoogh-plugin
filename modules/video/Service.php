<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Video;
defined('ABSPATH') || exit;
final class Service
{
    public static function save(int $id,array $data):void{$clean=[];if(array_key_exists('url',$data))$clean['url']=esc_url_raw((string)$data['url']);if(array_key_exists('poster',$data))$clean['poster']=absint($data['poster']);if(array_key_exists('gallery',$data))$clean['gallery']=array_values(array_filter(array_map('absint',(array)$data['gallery'])));Repository::save($id,$clean);}
}
