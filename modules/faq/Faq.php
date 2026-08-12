<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Faq;
defined('ABSPATH') || exit;
/** Backward-compatible FAQ facade. */
final class Faq
{
    public static function get(int $termId):array{return Repository::get($termId);}
    public static function save(int $termId,array $items):void{Service::save($termId,$items);}
    public static function delete(int $termId):void{Repository::delete($termId);}
    public static function exists(int $termId):bool{return self::get($termId)!==[];}
    public static function hasFaq(int $termId):bool{return self::exists($termId);}
    public static function count(int $termId):int{return count(self::get($termId));}
}
