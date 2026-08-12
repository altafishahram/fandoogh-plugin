<?php
declare(strict_types=1);
namespace Fandoogh\Modules\Faq;
defined('ABSPATH') || exit;
final class Renderer
{
    public static function render(int $termId,bool $firstOpen=true):string{$html='';foreach(Service::sanitize(Faq::get($termId)) as $index=>$item){$q=(string)$item['question'];$a=(string)$item['answer'];if($q===''&&$a==='')continue;$html.='<details class="fa-faq-item"'.($firstOpen&&$index===0?' open':'').'><summary class="fa-faq-question">'.esc_html($q).'<span class="fa-faq-toggle" aria-hidden="true"></span></summary><div class="fa-faq-answer">'.wpautop($a).'</div></details>';}if($html==='')return '';Schema::markVisible($termId);return '<div class="fa-faq">'.$html.'</div>';}
}
