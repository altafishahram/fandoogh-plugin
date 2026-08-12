(function ($) {
    'use strict';

    function initializeProductSearch() {
        $(document.body).trigger('wc-enhanced-select-init');
    }

    function notice(message, error) {
        $('#fa-fixed-prices-form .fa-ajax-notice')
            .removeClass('is-success is-error')
            .addClass(error ? 'is-error' : 'is-success')
            .text(message);
    }

    $(document).on('click', '#fa-add-fixed-price', function () {
        const template = document.getElementById('fa-fixed-price-template');
        if (!template) return;

        const index = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 10000);
        const $row = $(template.innerHTML.replaceAll('__INDEX__', index));
        $row.find('[data-fixed-price-id]').val('fee-' + index);
        $('#fa-fixed-prices-list').append($row);
        initializeProductSearch();
    });

    $(document).on('click', '.fa-remove-fixed-price', function () {
        if (window.confirm(faCalculatorAdmin.removeConfirm)) {
            $(this).closest('[data-fixed-price-row]').remove();
        }
    });

    $(document).on('submit', '#fa-fixed-prices-form', function (event) {
        event.preventDefault();
        const $form = $(this);
        const $button = $form.find('button[type="submit"]');
        const data = $form.serializeArray();

        data.push({name: 'action', value: 'fa_save_fixed_prices'});
        data.push({name: 'nonce', value: faCalculatorAdmin.nonce});
        $button.prop('disabled', true);
        $form.attr('aria-busy', 'true');
        notice('در حال ذخیره…', false);

        $.ajax({
            url: faCalculatorAdmin.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: $.param(data)
        }).done(function (response) {
            if (!response.success) {
                notice(response.data && response.data.message ? response.data.message : 'ذخیره قیمت‌ها انجام نشد.', true);
                return;
            }

            notice(response.data.message, false);
        }).fail(function (xhr) {
            const response = xhr.responseJSON;
            notice(response && response.data && response.data.message
                ? response.data.message
                : 'در ارتباط با سرور خطایی رخ داد.', true);
        }).always(function () {
            $button.prop('disabled', false);
            $form.removeAttr('aria-busy');
        });
    });

    $(document).on('fa:section-loaded', initializeProductSearch);
    $(initializeProductSearch);

    $(document).on('input', '#fa-opacity-slider', function() {
        $('#fa-opacity-val').text($(this).val() + '%');
    });
})(jQuery);
