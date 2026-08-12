<?php

declare(strict_types=1);

namespace Fandoogh\AdminTheme;

use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class SettingsRepository
{
    /** @var array<string, mixed>|null */
    private ?array $settingsCache = null;

    /** @var array<string, string>|null */
    private ?array $assetCache = null;

    /** @return array<string, mixed> */
    public function get(): array
    {
        if ($this->settingsCache === null) {
            $stored = get_option(Options::ADMIN_THEME_SETTINGS, []);
            $this->settingsCache = (new SettingsSanitizer())->sanitize(is_array($stored) ? $stored : []);
        }
        return $this->settingsCache;
    }

    /** @param array<string, mixed> $settings */
    public function save(array $settings): void
    {
        update_option(Options::ADMIN_THEME_SETTINGS, $settings, false);
        $this->settingsCache = $settings;
    }

    /** @return array<string, string> */
    public function asset(): array
    {
        if ($this->assetCache === null) {
            $asset = get_option(Options::ADMIN_THEME_ASSET, []);
            $this->assetCache = is_array($asset) ? array_map('strval', $asset) : [];
        }
        return $this->assetCache;
    }

    /** @param array<string, string> $asset */
    public function saveAsset(array $asset): void
    {
        update_option(Options::ADMIN_THEME_ASSET, $asset, false);
        update_option(Options::ADMIN_THEME_SCHEMA_VERSION, SettingsSchema::VERSION, false);
        $this->assetCache = $asset;
    }
}
