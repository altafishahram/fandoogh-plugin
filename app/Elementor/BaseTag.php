<?php
declare(strict_types=1);
namespace Fandoogh\Elementor;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;
defined('ABSPATH') || exit;
abstract class BaseTag extends Data_Tag
{
    public function get_group(): array|string { return 'fa'; }
    public function get_categories(): array { return [Module::TEXT_CATEGORY]; }
    protected function getCurrentTerm(): ?\WP_Term { $term=get_queried_object(); return $term instanceof \WP_Term?$term:null; }
    protected function getCurrentTermId(): int { return (int)($this->getCurrentTerm()?->term_id??0); }
    protected function currentPostId(): int { return (int)get_the_ID(); }
    protected function setting(string $key,mixed $default=null): mixed { $settings=$this->get_settings(); return $settings[$key]??$default; }
    protected function isPreview(): bool
    { return isset(\Elementor\Plugin::$instance->editor)&&\Elementor\Plugin::$instance->editor->is_edit_mode(); }
}
