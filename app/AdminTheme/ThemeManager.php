<?php

declare(strict_types=1);

namespace Fandoogh\AdminTheme;

use Fandoogh\Core\Constants\Assets;
use Fandoogh\Core\Constants\Options;

defined('ABSPATH') || exit;

final class ThemeManager
{
    private const LOCK_TTL = 120;

    public function __construct(
        private readonly SettingsRepository $repository = new SettingsRepository(),
        private readonly ThemeCssGenerator $generator = new ThemeCssGenerator()
    ) {
    }

    /** @return array<string, mixed> */
    public function settings(): array
    {
        return $this->repository->get();
    }

    /** @return array<string, mixed>|\WP_Error */
    public function save(array $input): array|\WP_Error
    {
        if (! $this->acquireLock()) {
            return new \WP_Error('fa_theme_locked', 'تولید پوسته دیگری در حال انجام است؛ چند لحظه بعد دوباره تلاش کنید.');
        }

        try {
            $settings = (new SettingsSanitizer())->sanitize($input);
            $asset = $this->generator->generate($settings);
            if (is_wp_error($asset)) {
                return $asset;
            }

            $current = $this->repository->asset();
            $currentFilename = (string) ($current['filename'] ?? '');
            $asset['previous'] = $currentFilename !== '' && $currentFilename !== $asset['filename']
                ? $currentFilename
                : (string) ($current['previous'] ?? '');
            $asset['build'] = FA_BUILD;
            $this->repository->save($settings);
            $this->repository->saveAsset($asset);
            $this->generator->cleanup([$asset['filename'], $asset['previous']]);

            $resolved = $this->generator->resolve($asset['filename']);
            if ($resolved !== null) {
                $asset['url'] = $resolved['url'];
            }

            return ['settings' => $settings, 'asset' => $asset];
        } finally {
            delete_option(Options::ADMIN_THEME_GENERATION_LOCK);
        }
    }

    public function ensure(): void
    {
        $settings = $this->repository->get();
        if (! $settings['enabled']) {
            return;
        }

        $asset = $this->repository->asset();
        $resolved = $this->generator->resolve((string) ($asset['filename'] ?? ''));
        $schema = (string) get_option(Options::ADMIN_THEME_SCHEMA_VERSION, '');
        if ($schema === SettingsSchema::VERSION && ($asset['build'] ?? '') === FA_BUILD && $resolved !== null && is_file($resolved['path'])) {
            return;
        }

        $result = $this->save($settings);
        if (is_wp_error($result) && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Fandoogh admin theme generation failed: ' . $result->get_error_message());
        }
    }

    public function enqueue(): void
    {
        $settings = $this->repository->get();
        if (! $settings['enabled']) {
            return;
        }

        $asset = $this->repository->asset();
        $resolved = $this->generator->resolve((string) ($asset['filename'] ?? ''));
        if ($resolved === null || ! is_file($resolved['path'])) {
            $resolved = $this->generator->resolve((string) ($asset['previous'] ?? ''));
        }
        if ($resolved === null || ! is_file($resolved['path'])) {
            return;
        }

        wp_enqueue_style(
            Assets::ADMIN_THEME,
            $resolved['url'],
            [Assets::ADMIN_MODULES],
            (string) ($asset['version'] ?? FA_BUILD)
        );
    }

    private function acquireLock(): bool
    {
        $now = time();
        $lock = (int) get_option(Options::ADMIN_THEME_GENERATION_LOCK, 0);
        if ($lock > 0 && ($now - $lock) < self::LOCK_TTL) {
            return false;
        }
        if ($lock > 0) {
            delete_option(Options::ADMIN_THEME_GENERATION_LOCK);
        }
        return add_option(Options::ADMIN_THEME_GENERATION_LOCK, $now, '', false);
    }
}
