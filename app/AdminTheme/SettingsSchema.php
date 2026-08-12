<?php

declare(strict_types=1);

namespace Fandoogh\AdminTheme;

defined('ABSPATH') || exit;

final class SettingsSchema
{
    public const VERSION = '1.0.0';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return self::presets()['glass'];
    }

    /** @return array<string, array<string, mixed>> */
    public static function presets(): array
    {
        return [
            'glass' => [
                'enabled' => true,
                'preset' => 'glass',
                'scheme' => 'light',
                'font' => 'vazirmatn',
                'primary' => '#118eea',
                'secondary' => '#15c5eb',
                'background' => '#e9f4fb',
                'surface' => '#ffffff',
                'text' => '#152536',
                'muted' => '#607286',
                'border' => '#d7e7f2',
                'success' => '#1caa61',
                'warning' => '#c66b12',
                'danger' => '#d63638',
                'dark_background' => '#0e1b29',
                'dark_surface' => '#17283a',
                'dark_text' => '#f4f8fc',
                'dark_muted' => '#a9bac9',
                'radius' => 22,
                'blur' => 18,
                'glass_opacity' => 64,
            ],
            'midnight' => [
                'enabled' => true,
                'preset' => 'midnight',
                'scheme' => 'dark',
                'font' => 'vazirmatn',
                'primary' => '#38bdf8',
                'secondary' => '#818cf8',
                'background' => '#0b1220',
                'surface' => '#172033',
                'text' => '#f8fafc',
                'muted' => '#a6b2c3',
                'border' => '#334155',
                'success' => '#34d399',
                'warning' => '#fbbf24',
                'danger' => '#fb7185',
                'dark_background' => '#0b1220',
                'dark_surface' => '#172033',
                'dark_text' => '#f8fafc',
                'dark_muted' => '#a6b2c3',
                'radius' => 20,
                'blur' => 20,
                'glass_opacity' => 58,
            ],
            'clean' => [
                'enabled' => true,
                'preset' => 'clean',
                'scheme' => 'light',
                'font' => 'vazirmatn',
                'primary' => '#2563eb',
                'secondary' => '#0ea5e9',
                'background' => '#f1f5f9',
                'surface' => '#ffffff',
                'text' => '#0f172a',
                'muted' => '#64748b',
                'border' => '#dbe3ec',
                'success' => '#16a34a',
                'warning' => '#d97706',
                'danger' => '#dc2626',
                'dark_background' => '#111827',
                'dark_surface' => '#1f2937',
                'dark_text' => '#f9fafb',
                'dark_muted' => '#b6c0cf',
                'radius' => 12,
                'blur' => 8,
                'glass_opacity' => 88,
            ],
        ];
    }

    /** @return array<string, string> */
    public static function fonts(): array
    {
        return [
            'vazirmatn' => 'Vazirmatn',
            'tahoma' => 'Tahoma',
            'system' => 'System UI',
        ];
    }
}
