<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
use Fandoogh\Core\Constants\Shortcodes as ShortcodeNames;
use Fandoogh\Core\Managers\ProjectManager;
defined('ABSPATH') || exit;
final class Shortcodes
{
    private const MAP=[ShortcodeNames::PROJECT_NAME=>'name',ShortcodeNames::PROJECT_IMAGE=>'image',ShortcodeNames::PROJECT_CONTRACTOR=>'contractor',ShortcodeNames::PROJECT_DESCRIPTION=>'description',ShortcodeNames::PROJECT_ADDRESS=>'address',ShortcodeNames::PROJECT_PRODUCT_CATEGORIES=>'productCategories',ShortcodeNames::PROJECT_CATEGORIES=>'categories',ShortcodeNames::PROJECT_VIDEO=>'video',ShortcodeNames::PROJECT_GALLERY=>'gallery'];
    public function boot():void{foreach(self::MAP as $tag=>$method)add_shortcode($tag,fn($atts=[])=>$this->render((array)$atts,$method));}
    private function render(array $atts,string $method):string{$a=shortcode_atts(['id'=>0],$atts);$id=absint($a['id'])?:(int)get_the_ID();return ProjectManager::isProject($id)?Renderer::$method($id):'';}
}
