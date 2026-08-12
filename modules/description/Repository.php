<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Description;
use Fandoogh\Core\Constants\Meta\DescriptionMeta;
use Fandoogh\Core\Managers\MetaManager;
defined('ABSPATH') || exit;
final class Repository
{
    public static function get(int $termId):string{return (string)MetaManager::getTermMeta($termId,DescriptionMeta::DESCRIPTION,'');}
    public static function save(int $termId,string $content):void{MetaManager::updateTermMeta($termId,DescriptionMeta::DESCRIPTION,$content);}
    public static function delete(int $termId):void{MetaManager::deleteTermMeta($termId,DescriptionMeta::DESCRIPTION);}
}
