<?php

declare(strict_types=1);

namespace Fandoogh\AdminTheme;

defined('ABSPATH') || exit;

final class SettingsSanitizer
{
    /** @return array<string, mixed> */
    public function sanitize(array $input): array
    {
        $defaults = SettingsSchema::defaults();
        $presets = SettingsSchema::presets();
        $preset = sanitize_key($this->string($input['preset'] ?? 'glass'));
        if (! isset($presets[$preset])) {
            $preset = 'glass';
        }
        $base = array_merge($defaults, $presets[$preset]);
        $result = $base;
        $result['enabled'] = array_key_exists('enabled', $input) ? ! empty($input['enabled']) : (bool) $base['enabled'];
        $result['preset'] = $preset;
        $scheme = $this->string($input['scheme'] ?? '');
        $font = $this->string($input['font'] ?? '');
        $result['scheme'] = in_array($scheme, ['light', 'dark', 'system'], true) ? $scheme : (string) $base['scheme'];
        $result['font'] = array_key_exists($font, SettingsSchema::fonts()) ? $font : (string) $base['font'];

        foreach (['primary', 'secondary', 'background', 'surface', 'text', 'muted', 'border', 'success', 'warning', 'danger', 'dark_background', 'dark_surface', 'dark_text', 'dark_muted'] as $key) {
            $color = sanitize_hex_color($this->string($input[$key] ?? ''));
            $result[$key] = $color ?: $base[$key];
        }

        $radius = is_numeric($input['radius'] ?? null) ? (int) $input['radius'] : (int) $base['radius'];
        $blur = is_numeric($input['blur'] ?? null) ? (int) $input['blur'] : (int) $base['blur'];
        $opacity = is_numeric($input['glass_opacity'] ?? null) ? (int) $input['glass_opacity'] : (int) $base['glass_opacity'];
        $result['radius'] = max(0, min(40, $radius));
        $result['blur'] = max(0, min(30, $blur));
        $result['glass_opacity'] = max(20, min(100, $opacity));

        if ($this->contrast((string) $result['text'], (string) $result['background']) < 4.5) {
            $result['text'] = $this->readableText((string) $result['background']);
        }
        if ($this->contrast((string) $result['dark_text'], (string) $result['dark_background']) < 4.5) {
            $result['dark_text'] = $this->readableText((string) $result['dark_background']);
        }

        return $result;
    }

    private function string(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    public function readableText(string $background): string
    {
        return $this->contrast('#ffffff', $background) >= $this->contrast('#0f172a', $background)
            ? '#ffffff' : '#0f172a';
    }

    private function contrast(string $first, string $second): float
    {
        $a = $this->luminance($first);
        $b = $this->luminance($second);
        return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
    }

    private function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $channels = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        $channels = array_map(static function (int $value): float {
            $value /= 255;
            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, $channels);
        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}
