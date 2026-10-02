<?php
declare(strict_types=1);
namespace Fandoogh\Projects;
use Fandoogh\Core\Constants\ContentTypes;
use Fandoogh\Core\Constants\Meta\ProjectMeta;
defined('ABSPATH') || exit;
final class Repository
{
    public static function get(int $id): array { $data=get_post_meta($id,ProjectMeta::DATA,true); return is_array($data)?$data:[]; }
    public static function save(int $id,array $data): void {
        update_post_meta($id,ProjectMeta::DATA,$data);
        self::syncRelations($id, $data);
    }
    public static function syncRelations(int $id, array $data): void {
        update_post_meta($id, '_fa_project_customer', absint($data['customer_id'] ?? 0));
        delete_post_meta($id, '_fa_project_product');
        foreach ((array) ($data['product_ids'] ?? []) as $product) add_post_meta($id, '_fa_project_product', absint($product));
    }
    public static function field(int $id,string $field,mixed $default=null): mixed { $data=self::get($id); return $data[$field]??$default; }
    public static function isProject(int $id): bool { return get_post_type($id)===ContentTypes::PROJECT; }
}
