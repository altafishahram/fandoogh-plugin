<?php
declare(strict_types=1);
namespace Fandoogh\Core\Managers;
use Fandoogh\Projects\Repository;
use Fandoogh\Projects\Service;
defined('ABSPATH') || exit;
/** Backward-compatible facade for project integrations. */
final class ProjectManager
{
    public static function isProject(int $id): bool{return Repository::isProject($id);}
    public static function sanitize(mixed $value): array{return Service::sanitize($value);}
    public static function save(int $id,array $data): void{Service::save($id,$data);}
    public static function get(int $id): array{return Repository::get($id);}
    public static function all(int $id): array{return Repository::get($id);}
    public static function field(int $id,string $field,mixed $default=null): mixed{return Repository::field($id,$field,$default);}
    public static function title(int $id): string{return get_the_title($id);}
    public static function contractor(int $id): string{return (string)Repository::field($id,'contractor','');}
    public static function excerpt(int $id): string{return (string)Repository::field($id,'excerpt','');}
    public static function address(int $id): string{return (string)Repository::field($id,'address','');}
    public static function video(int $id): string{return (string)Repository::field($id,'video','');}
    public static function gallery(int $id): array{return (array)Repository::field($id,'gallery',[]);}
    public static function categories(int $id): array{return (array)Repository::field($id,'categories',[]);}
}
