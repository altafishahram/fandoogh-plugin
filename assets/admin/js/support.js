(function () {
    'use strict';
    // Delegation also covers dashboard sections loaded with AJAX.
    document.addEventListener('click', async function (event) {
        var button = event.target.closest('[data-fa-donation-copy]');
        if (!button) return;
        var status = button.parentElement.querySelector('.fa-donation-status');
        try {
            await navigator.clipboard.writeText(button.dataset.faDonationCopy);
            status.textContent = 'شماره کارت کپی شد.';
        } catch (error) {
            var number = button.parentElement.querySelector('.fa-donation-bank__number');
            var range = document.createRange();
            range.selectNodeContents(number);
            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            status.textContent = 'شماره کارت انتخاب شد؛ آن را دستی کپی کنید.';
        }
    });
}());
