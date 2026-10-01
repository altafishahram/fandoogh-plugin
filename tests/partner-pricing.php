<?php

declare(strict_types=1);

// Isolated contract tests. WordPress/WooCommerce integration still requires a test shop.
define('ABSPATH', __DIR__);
$hooks = []; $users = []; $viewer = 0; $adminRequest = false; $ajax = false;
$permissions = []; $products = []; $cleared = []; $private = []; $passwords = []; $checks = 0;
function add_filter($hook, $callback, $priority = 10, $args = 1) { $GLOBALS['hooks'][$hook][] = [$callback, $args]; }
function add_action($hook, $callback, $priority = 10, $args = 1) { add_filter($hook, $callback, $priority, $args); }
function apply_filters($hook, $value, ...$args) { foreach ($GLOBALS['hooks'][$hook] ?? [] as [$callback, $count]) { $value = $callback(...array_slice([$value, ...$args], 0, $count)); } return $value; }
function get_current_user_id() { return $GLOBALS['viewer']; }
function get_user_meta($id, $key, $single) { return $GLOBALS['users'][$id][$key] ?? ''; }
function update_user_meta($id, $key, $value) { $GLOBALS['users'][$id][$key] = $value; }
function delete_user_meta($id, $key) { unset($GLOBALS['users'][$id][$key]); }
function current_user_can($cap, ...$args) { return $GLOBALS['permissions'][$cap] ?? false; }
function is_admin() { return $GLOBALS['adminRequest']; }
function wp_doing_ajax() { return $GLOBALS['ajax']; }
function absint($v) { return abs((int) $v); }
function sanitize_text_field($v) { return strip_tags(trim($v)); }
function wp_unslash($v) { return $v; }
function wp_verify_nonce($nonce, $action) { return $nonce === 'valid:' . $action; }
function __($value, $domain = '') { return $value; }
function wc_format_decimal($value) { return is_numeric($value) ? (string) $value : ''; }
function wc_get_product($id) { return $GLOBALS['products'][$id] ?? false; }
function wc_delete_product_transients($id) { $GLOBALS['cleared'][] = $id; }
function get_post_status($id) { return !empty($GLOBALS['private'][$id]) ? 'draft' : 'publish'; }
function post_password_required($id) { return !empty($GLOBALS['passwords'][$id]); }
function get_queried_object_id() { return 1; }
function esc_attr($value) { return htmlspecialchars($value, ENT_QUOTES); }
function esc_html($value) { return htmlspecialchars($value, ENT_QUOTES); }
function esc_url($value) { return htmlspecialchars($value, ENT_QUOTES); }
function wp_kses_post($value) { return $value; }
function shortcode_atts($defaults, $args, $name) { return array_merge($defaults, array_intersect_key($args, $defaults)); }
class WC_Admin_Meta_Boxes { public static array $errors = []; public static function add_error($message) { self::$errors[] = $message; } }
class WC_Product {
    public array $meta = []; public string $status = 'publish'; public bool $stock = true;
    public function __construct(public int $id, public string $type = 'simple', public string $retail = '100', public int $parent = 0) {}
    public function get_id() { return $this->id; }
    public function get_parent_id() { return $this->parent; }
    public function is_type($types) { return in_array($this->type, (array) $types, true); }
    public function get_meta($key, $single = true, $context = 'view') { return $this->meta[$key] ?? ''; }
    public function update_meta_data($key, $value) { $this->meta[$key] = $value; }
    public function delete_meta_data($key) { unset($this->meta[$key]); }
    public function get_price($context = 'view') { return $context === 'edit' ? $this->retail : apply_filters('woocommerce_product' . ($this->type === 'variation' ? '_variation' : '') . '_get_price', $this->retail, $this); }
    public function get_status() { return $this->status; }
    public function get_price_html() { return '<span>' . $this->get_price() . '</span>'; }
    public function is_purchasable() { return $this->retail !== ''; }
    public function is_in_stock() { return $this->stock; }
    public function add_to_cart_url() { return '/?add-to-cart=' . $this->id; }
    public function add_to_cart_text() { return 'Buy'; }
}
foreach (['Access', 'Pricing', 'Admin', 'Display'] as $file) require dirname(__DIR__) . '/app/CRM/PartnerPricing/' . $file . '.php';
use Fandoogh\CRM\PartnerPricing\Access;
use Fandoogh\CRM\PartnerPricing\Pricing;
use Fandoogh\CRM\PartnerPricing\Admin;
use Fandoogh\CRM\PartnerPricing\Display;
function check($condition, $message) { ++$GLOBALS['checks']; if (!$condition) { throw new RuntimeException($message); } }
$pricing = new Pricing(); $pricing->boot(); $admin = new Admin();
$simple = $products[1] = new WC_Product(1); $simple->meta[Pricing::PRICE_META] = '75.50';
$variation = $products[2] = new WC_Product(2, 'variation', '140', 3); $variation->meta[Pricing::PRICE_META] = '90';
$users[5][Access::USER_META] = 'yes';
check($simple->get_price() === '100', 'Guest must pay retail');
check(Display::render(['product_id' => 1, 'force' => 'yes']) === '', 'Guest shortcode must not leak price');
$viewer = 6;
check($simple->get_price() === '100', 'Regular user must pay retail');
check(Display::render(['product_id' => 1]) === '', 'Regular user shortcode hidden');
$retailHash = $pricing->cacheHash([]); $viewer = 5;
check($simple->get_price() === '75.50', 'Partner gets simple price');
check($simple->get_price('edit') === '100', 'Retail storage never changed');
check($variation->get_price() === '90', 'Partner gets variation-specific price');
foreach (['price', 'regular_price', 'sale_price'] as $field) {
    check(apply_filters('woocommerce_variation_prices_' . $field, '140', $variation) === '90', 'Variable price range filter: ' . $field);
}
check($pricing->cacheHash([]) !== $retailHash, 'Separate partner and retail range caches');
$html = Display::render(['product_id' => 2, 'label' => '<b>Partner</b>', 'show_cart' => 'yes']);
check(str_contains($html, '90') && str_contains($html, 'add-to-cart=2'), 'Arbitrary page can render specified variation and purchase URL');
$private[3] = true;
check(Display::render(['product_id' => 2]) === '', 'Draft variation parent must stay private'); unset($private[3]);
$passwords[3] = true;
check(Display::render(['product_id' => 2]) === '', 'Password-protected parent must stay private'); unset($passwords[3]);
$simple->stock = false;
check(!str_contains(Display::render(['product_id' => 1, 'show_cart' => 'yes']), 'add-to-cart'), 'No buy button when out of stock'); $simple->stock = true;
$simple->meta[Pricing::PRICE_META] = '';
check($simple->get_price() === '100', 'Empty price falls back to retail');
$simple->meta[Pricing::PRICE_META] = '0';
check($simple->get_price() === '0', 'Explicit zero works');
foreach (['-1', 'bad', [], '1e999'] as $invalid) {
    $simple->meta[Pricing::PRICE_META] = $invalid;
    check($simple->get_price() === '100', 'Invalid stored price rejected');
}
$simple->meta[Pricing::PRICE_META] = '75.50';
$adminRequest = true;
check($simple->get_price() === '100', 'Admin editing sees retail'); $ajax = true;
check($simple->get_price() === '75.50', 'Front-end AJAX uses partner price'); $adminRequest = false; $ajax = false;
unset($users[5][Access::USER_META]);
check($simple->get_price() === '100', 'Revocation immediately affects same product/cart object');
check($pricing->cacheHash([]) === $retailHash, 'Revocation uses retail cache');
check(Display::render(['product_id' => 1]) === '', 'Revocation hides shortcode');
$_POST = ['fa_partner_user_nonce' => 'valid:fa_partner_user_5', 'fa_is_partner' => 'yes'];
$admin->saveUser(5);
check(!Access::isPartner(), 'User cannot self-assign via forged POST');
$permissions = ['manage_woocommerce' => true, 'edit_user' => true];
$_POST['fa_partner_user_nonce'] = 'invalid'; $admin->saveUser(5);
check(!Access::isPartner(), 'Invalid user nonce rejected');
$_POST['fa_partner_user_nonce'] = 'valid:fa_partner_user_6'; $admin->saveUser(5);
check(!Access::isPartner(), 'Nonce bound to target user');
$_POST['fa_partner_user_nonce'] = 'valid:fa_partner_user_5'; $admin->saveUser(5);
check(Access::isPartner(), 'Shop manager can approve an editable customer');
unset($_POST['fa_is_partner']); $admin->saveUser(5);
check(!Access::isPartner(), 'Shop manager can revoke');
$permissions = ['manage_woocommerce' => true, 'edit_user' => false];
check(!Access::canManage(5), 'Manage WooCommerce alone cannot bypass edit-user restrictions');
$permissions = ['edit_post' => true];
$_POST = [Pricing::PRICE_META => '20']; $admin->saveProduct($simple);
check($simple->meta[Pricing::PRICE_META] === '75.50', 'No product nonce leaves stored price intact');
$_POST['fa_partner_price_nonce'] = 'valid:fa_partner_price_product'; $admin->saveProduct($simple);
check($simple->meta[Pricing::PRICE_META] === '20', 'Product save writes private metadata');
$_POST[Pricing::PRICE_META] = '-1'; $admin->saveProduct($simple);
check($simple->meta[Pricing::PRICE_META] === '20' && count(WC_Admin_Meta_Boxes::$errors) === 1, 'Invalid input retains previous value');
$_POST[Pricing::PRICE_META] = ''; $admin->saveProduct($simple);
check(!isset($simple->meta[Pricing::PRICE_META]), 'Blank field deletes override');
$_POST = ['fa_partner_variation_nonce' => [0 => 'valid:fa_partner_price_variation_2'], 'fa_partner_variation_price' => [0 => '85']];
$admin->saveVariation($variation, 0);
check($variation->meta[Pricing::PRICE_META] === '85', 'Independent variation save');
$pricing->clearVariationCache(2);
check($cleared === [3], 'Variation edits invalidate parent range cache');
echo "Partner pricing: {$checks} checks passed (isolated contracts).\n";
