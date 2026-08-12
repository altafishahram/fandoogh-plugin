<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Description;
defined('ABSPATH') || exit;
final class Service
{
    public static function save(int $termId,string $content):void{Repository::save($termId,wp_kses_post($content));}
}
