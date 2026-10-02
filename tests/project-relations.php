<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
$types = [1 => 'fa_project', 2 => 'fa_customer', 3 => 'product', 4 => 'product', 5 => 'post'];
$meta = [];
function get_post_type($id) { global $types; return $types[$id] ?? false; }
function absint($value) { return abs((int) $value); }
function sanitize_text_field($value) { return strip_tags($value); }
function sanitize_textarea_field($value) { return strip_tags($value); }
function wp_kses_post($value) { return $value; }
function esc_url_raw($value) { return $value; }
function get_post_meta($id, $key, $single = false) { global $meta; return $single ? ($meta[$id][$key][0] ?? '') : ($meta[$id][$key] ?? []); }
function update_post_meta($id, $key, $value) { global $meta; $meta[$id][$key] = [$value]; }
function delete_post_meta($id, $key) { global $meta; unset($meta[$id][$key]); }
function add_post_meta($id, $key, $value) { global $meta; $meta[$id][$key][] = $value; }
require __DIR__ . '/../app/Core/Constants/ContentTypes.php';
require __DIR__ . '/../app/Core/Constants/Meta/ProjectMeta.php';
require __DIR__ . '/../app/Projects/Repository.php';
require __DIR__ . '/../app/Projects/Service.php';
use Fandoogh\Projects\Service;
use Fandoogh\Projects\Repository;
function check(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
Service::save(1, ['contractor' => 'Old', 'customer_id' => 2, 'product_ids' => [3, '3', 4, 5, 999]]);
check(Repository::get(1)['product_ids'] === [3, 4], 'Invalid or duplicate products persisted');
check(get_post_meta(1, '_fa_project_product') === [3, 4], 'Reverse product index missing');
Service::save(1, ['address' => 'Updated']);
check(Repository::get(1)['customer_id'] === 2 && Repository::get(1)['product_ids'] === [3, 4], 'Partial save lost relations');
check(Repository::get(1)['contractor'] === 'Old', 'Partial save lost existing fields');
Service::save(1, ['customer_id' => 5, 'product_ids' => []]);
check(Repository::get(1)['customer_id'] === 0 && get_post_meta(1, '_fa_project_product') === [], 'Clearing relations left stale indexes');
Service::save(5, ['customer_id' => 2]);
check(!isset($meta[5]), 'Saved relations on wrong post type');
echo "Project relations test passed.\n";
