<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
defined('ABSPATH') || exit;
final class Service
{
    public static function sanitize(mixed $value): array { $d=is_array($value)?$value:[]; return ['contractor'=>sanitize_text_field((string)($d['contractor']??'')),'excerpt'=>wp_kses_post((string)($d['excerpt']??'')),'address'=>sanitize_textarea_field((string)($d['address']??'')),'video'=>esc_url_raw((string)($d['video']??'')),'gallery'=>array_values(array_filter(array_map('absint',(array)($d['gallery']??[])))),'categories'=>array_values(array_filter(array_map('absint',(array)($d['categories']??[]))))]; }
    public static function save(int $id,array $data): void { if(Repository::isProject($id))Repository::save($id,self::sanitize($data)); }
}
