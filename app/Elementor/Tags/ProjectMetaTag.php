<?php
declare(strict_types=1);
namespace Fandoogh\Elementor\Tags;
use Elementor\Controls_Manager;
use Fandoogh\Core\Managers\ProjectManager;
use Fandoogh\Elementor\BaseTag;
use Fandoogh\Projects\Taxonomy;
defined('ABSPATH') || exit;
final class ProjectMetaTag extends BaseTag
{
    public function get_name(): string { return 'fa-project-meta'; }
    public function get_title(): string { return __('اطلاعات پروژه', 'fandoogh'); }
    protected function register_controls(): void
    {
        $this->add_control('field',['label'=>__('فیلد','fandoogh'),'type'=>Controls_Manager::SELECT,'default'=>'name','options'=>[
            'name'=>__('نام پروژه','fandoogh'),'image'=>__('تصویر شاخص','fandoogh'),'contractor'=>__('مجری','fandoogh'),
            'description'=>__('توضیحات','fandoogh'),'address'=>__('آدرس','fandoogh'),'product_categories'=>__('دسته‌های محصول','fandoogh'),
            'project_categories'=>__('دسته‌بندی پروژه','fandoogh'),'video'=>__('ویدئو','fandoogh'),'gallery'=>__('گالری','fandoogh'),
        ]]);
    }
    public function get_value(array $options=[]): mixed
    {
        $id=(int)get_the_ID(); if(!ProjectManager::isProject($id))return '';
        return match((string)$this->setting('field','name')) {
            'image'=>(string)get_the_post_thumbnail_url($id,'full'),
            'contractor'=>ProjectManager::contractor($id), 'description'=>ProjectManager::excerpt($id),
            'address'=>ProjectManager::address($id), 'video'=>ProjectManager::video($id),
            'gallery'=>implode(', ',array_filter(array_map(fn($i)=>wp_get_attachment_image_url((int)$i,'full'),ProjectManager::gallery($id)))),
            'product_categories'=>$this->names($id,'product_cat',ProjectManager::categories($id)),
            'project_categories'=>$this->names($id,Taxonomy::NAME,[]),
            default=>ProjectManager::title($id),
        };
    }
    private function names(int $id,string $tax,array $ids): string
    { $terms=$ids?array_map(fn($i)=>get_term((int)$i,$tax),$ids):get_the_terms($id,$tax); return is_array($terms)?implode('، ',array_map(fn($t)=>$t instanceof \WP_Term?$t->name:'',$terms)):''; }
}
