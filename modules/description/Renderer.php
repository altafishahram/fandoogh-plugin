<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Description;
defined('ABSPATH') || exit;
final class Renderer
{
    public static function render(int $termId):string{$content=Description::get($termId);return $content===''?'':'<div class="fa-description-content">'.wpautop(wp_kses_post($content)).'</div>';}
}
