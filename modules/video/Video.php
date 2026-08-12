<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Video;
defined('ABSPATH') || exit;
/** Backward-compatible video facade. */
final class Video
{
    public static function get(int $id):array{return ['url'=>self::getUrl($id),'poster'=>self::getPoster($id),'gallery'=>self::getGallery($id)];}
    public static function save(int $id,array $data):void{Service::save($id,$data);}
    public static function delete(int $id):void{Repository::delete($id);}
    public static function getUrl(int $id):string{return Repository::url($id);}
    public static function getPoster(int $id):int{return Repository::poster($id);}
    public static function getGallery(int $id):array{return Repository::gallery($id);}
    public static function exists(int $id):bool{return self::getUrl($id)!=='';}
    public static function hasVideo(int $id):bool{return self::exists($id);}
}
