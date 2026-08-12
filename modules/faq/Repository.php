<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Faq;
use Fandoogh\Core\Constants\Meta\FaqMeta;
use Fandoogh\Core\Managers\MetaManager;
defined('ABSPATH') || exit;
final class Repository
{
    public static function get(int $termId): array{$items=MetaManager::getTermMeta($termId,FaqMeta::FAQ,[]);return is_array($items)?$items:[];}
    public static function save(int $termId,array $items):void{MetaManager::updateTermMeta($termId,FaqMeta::FAQ,$items);}
    public static function delete(int $termId):void{MetaManager::deleteTermMeta($termId,FaqMeta::FAQ);}
}
