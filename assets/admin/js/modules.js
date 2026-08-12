(function ($) {
    'use strict';

    function notice(message, error) {
        $('.fa-ajax-notice').first().removeClass('is-success is-error')
            .addClass(error ? 'is-error' : 'is-success').text(message);
    }

    $(document).on('change', '.fa-module-toggle', function () {
        const $toggle = $(this);
        const $card = $toggle.closest('.fa-module-card');
        const previous = !$toggle.prop('checked');
        $toggle.prop('disabled', true);
        $card.attr('aria-busy', 'true');

        $.ajax({
            url: faModules.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {action: 'fa_toggle_module', nonce: faModules.nonce, module: $toggle.data('module')}
        }).done(function (response) {
            if (!response.success) {
                $toggle.prop('checked', previous);
                notice(response.data && response.data.message ? response.data.message : 'تغییر وضعیت ماژول انجام نشد.', true);
                return;
            }
            $toggle.prop('checked', Boolean(response.data.status));
            $card.find('.fa-status').text(response.data.status ? 'فعال' : 'غیرفعال');
            notice(response.data.message, false);
        }).fail(function (xhr) {
            $toggle.prop('checked', previous);
            const response = xhr.responseJSON;
            notice(response && response.data && response.data.message ? response.data.message : 'در ارتباط با سرور خطایی رخ داد.', true);
        }).always(function () {
            $toggle.prop('disabled', false);
            $card.removeAttr('aria-busy');
        });
    });
})(jQuery);
