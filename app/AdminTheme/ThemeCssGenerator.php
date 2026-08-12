<?php

declare(strict_types=1);

namespace Fandoogh\AdminTheme;

defined('ABSPATH') || exit;

final class ThemeCssGenerator
{
    /** @param array<string, mixed> $settings @return array<string, string>|\WP_Error */
    public function generate(array $settings): array|\WP_Error
    {
        $location = $this->location();
        if (is_wp_error($location)) {
            return $location;
        }

        $css = $this->minify($this->compile($settings));
        if ($css === '') {
            return new \WP_Error('fa_theme_empty_css', 'خروجی CSS پوسته خالی است.');
        }

        $version = substr(hash('sha256', $css), 0, 12);
        $filename = 'generated-' . $version . '.css';
        $path = trailingslashit($location['dir']) . $filename;
        $temp = trailingslashit($location['dir']) . 'generated-' . wp_generate_password(12, false, false) . '.tmp';

        if (! is_file($path)) {
            $bytes = file_put_contents($temp, $css, LOCK_EX); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
            if ($bytes === false || $bytes !== strlen($css)) {
                @unlink($temp); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                return new \WP_Error('fa_theme_write_failed', 'ذخیره فایل CSS پوسته انجام نشد.');
            }
            if (! @rename($temp, $path)) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                @unlink($temp); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                return new \WP_Error('fa_theme_move_failed', 'نهایی‌سازی فایل CSS پوسته انجام نشد.');
            }
        }

        return ['filename' => $filename, 'version' => $version];
    }

    /** @return array{dir:string,url:string}|\WP_Error */
    public function location(): array|\WP_Error
    {
        $uploads = wp_get_upload_dir();
        if (! empty($uploads['error'])) {
            return new \WP_Error('fa_theme_uploads_error', (string) $uploads['error']);
        }

        $dir = trailingslashit((string) $uploads['basedir']) . 'fandoogh/admin-theme';
        if (! is_dir($dir) && ! wp_mkdir_p($dir)) {
            return new \WP_Error('fa_theme_directory_failed', 'پوشه فایل‌های پوسته قابل ساخت نیست.');
        }

        return ['dir' => $dir, 'url' => trailingslashit((string) $uploads['baseurl']) . 'fandoogh/admin-theme'];
    }

    /** @return array{path:string,url:string}|null */
    public function resolve(string $filename): ?array
    {
        if (! preg_match('/^generated-[a-f0-9]{12}\.css$/', $filename)) {
            return null;
        }
        $location = $this->location();
        if (is_wp_error($location)) {
            return null;
        }
        return [
            'path' => trailingslashit($location['dir']) . $filename,
            'url' => trailingslashit($location['url']) . $filename,
        ];
    }

    /** @param array<string, string> $keep */
    public function cleanup(array $keep): void
    {
        $location = $this->location();
        if (is_wp_error($location)) {
            return;
        }
        $allowed = array_filter($keep, static fn (string $file): bool => (bool) preg_match('/^generated-[a-f0-9]{12}\.css$/', $file));
        foreach ((array) glob(trailingslashit($location['dir']) . 'generated-*.css') as $file) {
            if (! in_array(basename((string) $file), $allowed, true)) {
                wp_delete_file((string) $file);
            }
        }
    }

    /** @param array<string, mixed> $settings */
    private function compile(array $settings): string
    {
        $font = match ($settings['font']) {
            'tahoma' => 'Tahoma,Arial,sans-serif',
            'system' => '-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif',
            default => '"Vazirmatn",Tahoma,Arial,sans-serif',
        };
        $fontFace = $settings['font'] === 'vazirmatn'
            ? '@font-face{font-family:"Vazirmatn";src:url("' . esc_url_raw(FA_URL . 'assets/admin/font/Vazirmatn-Regular.woff2') . '") format("woff2");font-weight:400;font-style:normal;font-display:swap;}'
            : '';
        $opacity = max(0.2, min(1, ((int) $settings['glass_opacity']) / 100));
        $surfaceRgb = $this->rgb((string) $settings['surface']);
        $borderRgb = $this->rgb((string) $settings['border']);
        $primaryText = (new SettingsSanitizer())->readableText((string) $settings['primary']);
        $lightVariables = $this->variables($settings, $surfaceRgb, $borderRgb, $opacity, $font, $primaryText, false);
        $darkVariables = $this->variables($settings, $this->rgb((string) $settings['dark_surface']), $borderRgb, $opacity, $font, $primaryText, true);
        $baseVariables = $settings['scheme'] === 'dark' ? $darkVariables : $lightVariables;
        $system = $settings['scheme'] === 'system' ? '@media(prefers-color-scheme:dark){.fa-admin-shell{' . $darkVariables . '}}' : '';

        return $fontFace . '.fa-admin-shell{' . $baseVariables . '}' . $system . $this->components();
    }

    /** @param array<string, mixed> $settings */
    private function variables(array $settings, string $surfaceRgb, string $borderRgb, float $opacity, string $font, string $primaryText, bool $dark): string
    {
        $background = $dark ? $settings['dark_background'] : $settings['background'];
        $surface = $dark ? $settings['dark_surface'] : $settings['surface'];
        $text = $dark ? $settings['dark_text'] : $settings['text'];
        $muted = $dark ? $settings['dark_muted'] : $settings['muted'];
        return '--fa-primary:' . $settings['primary'] . ';--fa-secondary:' . $settings['secondary'] . ';--fa-blue:var(--fa-primary);--fa-cyan:var(--fa-secondary);'
            . '--fa-background:' . $background . ';--fa-surface:' . $surface . ';--fa-ink:' . $text . ';--fa-muted:' . $muted . ';--fa-border:' . $settings['border'] . ';'
            . '--fa-success:' . $settings['success'] . ';--fa-warning:' . $settings['warning'] . ';--fa-danger:' . $settings['danger'] . ';--fa-primary-text:' . $primaryText . ';'
            . '--fa-radius:' . (int) $settings['radius'] . 'px;--fa-blur:' . (int) $settings['blur'] . 'px;--fa-shadow:color-mix(in srgb,var(--fa-ink) 12%,transparent);--fa-glass:rgba(' . $surfaceRgb . ',' . $opacity . ');'
            . '--fa-glass-strong:rgba(' . $surfaceRgb . ',' . min(1, $opacity + 0.16) . ');--fa-glass-border:rgba(' . $borderRgb . ',' . min(1, $opacity + 0.12) . ');'
            . '--fa-font:' . $font . ';color:var(--fa-ink);font-family:var(--fa-font);background:radial-gradient(circle at 8% 12%,color-mix(in srgb,var(--fa-primary) 24%,transparent),transparent 28%),radial-gradient(circle at 88% 84%,color-mix(in srgb,var(--fa-secondary) 20%,transparent),transparent 30%),linear-gradient(135deg,var(--fa-background),color-mix(in srgb,var(--fa-background) 88%,var(--fa-surface)));border-radius:var(--fa-radius);';
    }

    private function components(): string
    {
        return '.fa-admin-shell::before{background:color-mix(in srgb,var(--fa-primary) 38%,transparent)}.fa-admin-shell::after{background:color-mix(in srgb,var(--fa-secondary) 32%,transparent)}'
            . '.fa-admin-shell .fa-admin-sidebar,.fa-admin-shell .fa-admin-welcome,.fa-admin-shell .fa-admin-stat,.fa-admin-shell .fa-panel,.fa-admin-shell .fa-module-card{background:var(--fa-glass);border-color:var(--fa-glass-border);box-shadow:0 16px 38px var(--fa-shadow);backdrop-filter:blur(var(--fa-blur)) saturate(145%);-webkit-backdrop-filter:blur(var(--fa-blur)) saturate(145%);border-radius:var(--fa-radius);}'
            . '.fa-admin-shell .fa-admin-brand{background:var(--fa-glass-strong);border-color:var(--fa-glass-border);border-radius:calc(var(--fa-radius) - 2px);}'
            . '.fa-admin-shell .fa-admin-nav a,.fa-admin-shell .fa-quick-links a,.fa-admin-shell .fa-crm-features>div,.fa-admin-shell .fa-danger-setting{background:color-mix(in srgb,var(--fa-surface) 52%,transparent);color:var(--fa-ink);border-color:var(--fa-glass-border);border-radius:calc(var(--fa-radius) - 7px);}'
            . '.fa-admin-shell .fa-admin-nav a:hover,.fa-admin-shell .fa-admin-nav a.is-active,.fa-admin-shell .fa-quick-links a:hover,.fa-admin-shell .button-primary,.fa-admin-shell .fa-toggle input:checked+.fa-toggle-slider{background:linear-gradient(135deg,var(--fa-primary),var(--fa-secondary));color:var(--fa-primary-text);box-shadow:0 9px 22px color-mix(in srgb,var(--fa-primary) 28%,transparent);}'
            . '.fa-admin-shell h1,.fa-admin-shell h2,.fa-admin-shell h3,.fa-admin-shell strong,.fa-admin-shell .fa-status{color:var(--fa-ink);font-family:var(--fa-font);}'
            . '.fa-admin-shell p,.fa-admin-shell small,.fa-admin-shell .fa-admin-version{color:var(--fa-muted);}'
            . '.fa-admin-shell .fa-panel header,.fa-admin-shell .fa-module-footer,.fa-admin-shell .fa-health-list div{border-color:color-mix(in srgb,var(--fa-border) 58%,transparent);}'
            . '.fa-admin-shell .fa-module-icon,.fa-admin-shell .fa-admin-stat>.dashicons{color:var(--fa-primary);background:color-mix(in srgb,var(--fa-primary) 13%,transparent);}'
            . '.fa-admin-shell .is-ok{color:var(--fa-success);}.fa-admin-shell .is-warning{color:var(--fa-warning);}.fa-admin-shell .is-error{color:var(--fa-danger);}'
            . '.fa-admin-shell input[type=text],.fa-admin-shell input[type=number],.fa-admin-shell select,.fa-admin-shell textarea{background:color-mix(in srgb,var(--fa-surface) 78%,transparent);color:var(--fa-ink);border-color:var(--fa-border);border-radius:calc(var(--fa-radius) / 2);}';
    }

    private function minify(string $css): string
    {
        $css = preg_replace('!/\*.*?\*/!s', '', $css) ?? $css;
        $css = preg_replace('/\s+/', ' ', $css) ?? $css;
        return trim((string) preg_replace('/\s*([{}:;,])\s*/', '$1', $css));
    }

    private function rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        return hexdec(substr($hex, 0, 2)) . ',' . hexdec(substr($hex, 2, 2)) . ',' . hexdec(substr($hex, 4, 2));
    }
}
