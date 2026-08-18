(function () {
    'use strict';
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('.fa-oc-status-form, .fa-oc-note-form')) return;
        var button = form.querySelector('button[type="submit"]');
        if (button) { button.disabled = true; button.setAttribute('aria-busy', 'true'); }
    });
}());
