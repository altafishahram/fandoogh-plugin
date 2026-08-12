(function ($) {
    'use strict';

    const $content = $('#fa-admin-content');
    if (!$content.length) return;

    function showNotice($notice, message, error) {
        $notice.removeClass('is-success is-error').addClass(error ? 'is-error' : 'is-success').text(message);
    }

    async function request(data) {
        const response = await fetch(faAdmin.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: new URLSearchParams(data)
        });
        const result = await response.json().catch(function () { return null; });
        if (!response.ok || !result || !result.success) {
            throw new Error(result && result.data && result.data.message ? result.data.message : 'در ارتباط با سرور خطایی رخ داد.');
        }
        return result.data;
    }

    async function load(section, push) {
        if (typeof push === 'undefined') push = true;
        $content.addClass('fa-loading').attr('aria-busy', 'true');
        try {
            const data = await request({action: 'fa_load_admin_section', nonce: faAdmin.nonce, section: section});
            $content.html(data.html).attr('data-section', data.section);
            $('.fa-admin-nav-link').removeClass('is-active').removeAttr('aria-current')
                .filter('[data-section="' + data.section + '"]').addClass('is-active').attr('aria-current', 'page');
            if (push) history.pushState({faSection: data.section}, '', faAdmin.urls[data.section]);
            const heading = $content.find('h1').get(0);
            if (heading) heading.focus({preventScroll: true});
            document.dispatchEvent(new CustomEvent('fa:section-loaded', {detail: {section: data.section}}));
        } catch (error) {
            window.location.href = faAdmin.urls[section] || faAdmin.urls.dashboard;
        } finally {
            $content.removeClass('fa-loading').removeAttr('aria-busy');
        }
    }

    $(document).on('click', '.fa-admin-nav-link', function (event) {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        load($(this).data('section'));
    });

    $(document).on('submit', '#fa-settings-form', async function (event) {
        event.preventDefault();
        const $form = $(this);
        const $button = $form.find('button[type="submit"]');
        const $notice = $form.find('.fa-ajax-notice');
        $button.prop('disabled', true);
        $form.attr('aria-busy', 'true');
        showNotice($notice, 'در حال ذخیره…', false);
        try {
            const data = await request({
                action: 'fa_save_settings',
                nonce: faAdmin.nonce,
                delete_data: $form.find('[name="delete_data"]').is(':checked') ? '1' : '0'
            });
            showNotice($notice, data.message, false);
        } catch (error) {
            showNotice($notice, error.message, true);
        } finally {
            $button.prop('disabled', false);
            $form.removeAttr('aria-busy');
        }
    });

    window.addEventListener('popstate', function () {
        const page = new URL(window.location.href).searchParams.get('page');
        const map = {'fa-modules': 'modules', 'fa-product-seo': 'product_seo', 'fa-calculator': 'calculator', 'fa-crm': 'crm', 'fa-theme': 'theme', 'fa-settings': 'settings', 'fa-support': 'support'};
        load(map[page] || 'dashboard', false);
    });
})(jQuery);
