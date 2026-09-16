(function ($) {
    'use strict';

    // Activation uses a regular WordPress POST. This keeps the switch reliable
    // when AJAX is blocked, returns invalid JSON or an older script is cached.
    $(document).on('submit', '.fa-module-toggle-form', function () {
        const $form = $(this);
        $form.closest('.fa-module-card').attr('aria-busy', 'true');
        $form.find('button[type="submit"]').prop('disabled', true);
    });
})(jQuery);
