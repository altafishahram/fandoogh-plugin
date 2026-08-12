<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Description;
defined('ABSPATH') || exit;
/** Backward-compatible description facade. */
final class Description
{
    public static function get(int $termId):string{return Repository::get($termId);}
    public static function save(int $termId,string $content):void{Service::save($termId,$content);}
    public static function delete(int $termId):void{Repository::delete($termId);}
    public static function exists(int $termId):bool{return self::get($termId)!=='';}
    public static function hasDescription(int $termId):bool{return self::exists($termId);}
}
