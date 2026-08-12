<?php

declare(strict_types=1);

namespace Fandoogh\Customers;

use Fandoogh\Core\Constants\Shortcodes as ShortcodeNames;
use Fandoogh\Core\Managers\CustomerManager;

defined('ABSPATH') || exit;

final class Shortcodes
{
    private const MAP = [
        ShortcodeNames::CUSTOMER_NAME => 'name', ShortcodeNames::CUSTOMER_IMAGE => 'image',
        ShortcodeNames::CUSTOMER_DESCRIPTION => 'description', ShortcodeNames::CUSTOMER_ADDRESS => 'address',
        ShortcodeNames::CUSTOMER_PRODUCT_CATEGORIES => 'productCategories', ShortcodeNames::CUSTOMER_CATEGORIES => 'customerCategories',
        ShortcodeNames::CUSTOMER_VIDEO => 'video', ShortcodeNames::CUSTOMER_GALLERY => 'gallery',
    ];

    public function boot(): void { foreach(self::MAP as $tag=>$method){add_shortcode($tag,[$this,$method]);} }
    public function name(array $a=[]): string { return $this->render($a,'name'); }
    public function image(array $a=[]): string { return $this->render($a,'image'); }
    public function description(array $a=[]): string { return $this->render($a,'description'); }
    public function address(array $a=[]): string { return $this->render($a,'address'); }
    public function productCategories(array $a=[]): string { return $this->render($a,'productCategories'); }
    public function customerCategories(array $a=[]): string { return $this->render($a,'categories'); }
    public function video(array $a=[]): string { return $this->render($a,'video'); }
    public function gallery(array $a=[]): string { return $this->render($a,'gallery'); }
    private function render(array $atts,string $method): string { $id=$this->postId($atts); return $id>0?Renderer::$method($id):''; }
    private function postId(array $atts): int { $atts=shortcode_atts(['id'=>0],$atts); $id=absint($atts['id']) ?: (int)get_the_ID(); return CustomerManager::isCustomer($id)?$id:0; }
}
