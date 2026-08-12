<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Video;
use Fandoogh\Core\Constants\Meta\VideoMeta;
use Fandoogh\Core\Managers\MetaManager;
defined('ABSPATH') || exit;
final class Repository
{
    public static function url(int $id):string{return (string)MetaManager::getTermMeta($id,VideoMeta::URL,'');}
    public static function poster(int $id):int{return (int)MetaManager::getTermMeta($id,VideoMeta::POSTER,0);}
    public static function gallery(int $id):array{$data=MetaManager::getTermMeta($id,VideoMeta::GALLERY,[]);return is_array($data)?$data:[];}
    public static function save(int $id,array $data):void{if(array_key_exists('url',$data))MetaManager::updateTermMeta($id,VideoMeta::URL,$data['url']);if(array_key_exists('poster',$data))MetaManager::updateTermMeta($id,VideoMeta::POSTER,$data['poster']);if(array_key_exists('gallery',$data))MetaManager::updateTermMeta($id,VideoMeta::GALLERY,$data['gallery']);}
    public static function delete(int $id):void{MetaManager::deleteTermMeta($id,VideoMeta::URL);MetaManager::deleteTermMeta($id,VideoMeta::POSTER);MetaManager::deleteTermMeta($id,VideoMeta::GALLERY);}
}
