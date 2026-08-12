(function ($) {
    'use strict';

    function form() {
        return $('#fa-theme-form');
    }

    function collect() {
        const settings = {};
        form().find('[name^="settings["]').each(function () {
            const element = this;
            const match = element.name.match(/^settings\[([^\]]+)]$/);
            if (!match) return;
            if (element.type === 'checkbox') {
                settings[match[1]] = element.checked ? '1' : '';
            } else {
                settings[match[1]] = element.value;
            }
        });
        return settings;
    }

    function apply(settings) {
        Object.keys(settings).forEach(function (key) {
            const $field = form().find('[name="settings[' + key + ']"]');
            if (!$field.length) return;
            if ($field.is(':checkbox')) $field.prop('checked', Boolean(settings[key]));
            else $field.val(settings[key]);
        });
        updatePreview();
    }

    function rgba(hex, opacity) {
        const value = String(hex || '#ffffff').replace('#', '');
        const number = parseInt(value, 16);
        return 'rgba(' + ((number >> 16) & 255) + ',' + ((number >> 8) & 255) + ',' + (number & 255) + ',' + opacity + ')';
    }

    function updatePreview() {
        const settings = collect();
        const preview = document.getElementById('fa-theme-preview');
        if (!preview) return;
        const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = settings.scheme === 'dark' || (settings.scheme === 'system' && systemDark);
        const background = dark ? settings.dark_background : settings.background;
        const surface = dark ? settings.dark_surface : settings.surface;
        const text = dark ? settings.dark_text : settings.text;
        const muted = dark ? settings.dark_muted : settings.muted;
        const opacity = Math.max(.2, Math.min(1, Number(settings.glass_opacity || 64) / 100));
        const fonts = {
            vazirmatn: 'Vazirmatn, Tahoma, sans-serif',
            tahoma: 'Tahoma, Arial, sans-serif',
            system: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'
        };

        preview.style.setProperty('--preview-primary', settings.primary);
        preview.style.setProperty('--preview-secondary', settings.secondary);
        preview.style.setProperty('--preview-background', background);
        preview.style.setProperty('--preview-surface', surface);
        preview.style.setProperty('--preview-text', text);
        preview.style.setProperty('--preview-muted', muted);
        preview.style.setProperty('--preview-border', settings.border);
        preview.style.setProperty('--preview-glass', rgba(surface, opacity));
        preview.style.setProperty('--preview-radius', Number(settings.radius || 0) + 'px');
        preview.style.setProperty('--preview-blur', Number(settings.blur || 0) + 'px');
        preview.style.setProperty('--preview-font', fonts[settings.font] || fonts.vazirmatn);

        form().find('input[type="range"]').each(function () {
            form().find('[data-output="' + this.name.match(/\[([^\]]+)]/)[1] + '"]').text(this.value);
        });
    }

    function notice(message, error) {
        form().find('.fa-ajax-notice').removeClass('is-success is-error')
            .addClass(error ? 'is-error' : 'is-success').text(message);
    }

    function updateStylesheet(url, version, enabled) {
        if (!enabled) {
            const current = document.getElementById('fa-admin-theme-css');
            if (current) current.remove();
            return;
        }
        if (!url) return;
        const href = url + '?ver=' + encodeURIComponent(version || Date.now());
        let link = document.getElementById('fa-admin-theme-css');
        if (!link) {
            link = document.createElement('link');
            link.id = 'fa-admin-theme-css';
            link.rel = 'stylesheet';
            document.head.appendChild(link);
        }
        link.href = href;
    }

    function save(reset) {
        const $form = form();
        const $buttons = $form.find('button, .fa-theme-import');
        $buttons.prop('disabled', true);
        $form.attr('aria-busy', 'true');
        notice('در حال تولید فایل CSS…', false);
        return $.ajax({
            url: faTheme.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'fa_save_admin_theme',
                nonce: faTheme.nonce,
                settings: collect(),
                reset: reset ? '1' : ''
            }
        }).done(function (response) {
            if (!response.success) {
                notice(response.data && response.data.message ? response.data.message : 'ذخیره پوسته انجام نشد.', true);
                return;
            }
            apply(response.data.settings);
            updateStylesheet(response.data.cssUrl, response.data.version, response.data.settings.enabled);
            notice(response.data.message, false);
        }).fail(function (xhr) {
            const response = xhr.responseJSON;
            notice(response && response.data && response.data.message ? response.data.message : 'در ارتباط با سرور خطایی رخ داد.', true);
        }).always(function () {
            $buttons.prop('disabled', false);
            $form.removeAttr('aria-busy');
        });
    }

    $(document).on('submit', '#fa-theme-form', function (event) {
        event.preventDefault();
        save(false);
    });

    $(document).on('change input', '#fa-theme-form input, #fa-theme-form select', function () {
        if (this.id === 'fa-theme-preset' && faTheme.presets[this.value]) {
            const preset = Object.assign({}, faTheme.presets[this.value], {
                enabled: form().find('[name="settings[enabled]"]').is(':checked')
            });
            apply(preset);
            return;
        }
        updatePreview();
    });

    $(document).on('click', '#fa-theme-reset', function () {
        save(true);
    });

    $(document).on('click', '#fa-theme-export', function () {
        const blob = new Blob([JSON.stringify(collect(), null, 2)], {type: 'application/json'});
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = 'fandoogh-admin-theme.json';
        anchor.click();
        URL.revokeObjectURL(url);
    });

    $(document).on('change', '#fa-theme-import', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function () {
            try {
                const data = JSON.parse(String(reader.result));
                if (!data || typeof data !== 'object' || Array.isArray(data)) throw new Error();
                apply(data);
                notice('تنظیمات وارد شد؛ در حال تولید CSS…', false);
                save(false);
            } catch (error) {
                notice('فایل JSON معتبر نیست.', true);
            }
        };
        reader.readAsText(file);
    });

    document.addEventListener('fa:section-loaded', updatePreview);
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updatePreview);
    }
    $(updatePreview);
})(jQuery);
