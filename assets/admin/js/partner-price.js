(function () {
    'use strict';
    document.addEventListener('click', async function (event) {
        var button = event.target.closest('[data-fa-copy]');
        if (!button) return;
        var status = button.parentElement.querySelector('[role="status"]');
        try {
            await navigator.clipboard.writeText(button.dataset.faCopy);
            if (status) status.textContent = 'کپی شد.';
        } catch (error) {
            if (status) status.textContent = 'شورت‌کد را انتخاب و دستی کپی کنید.';
        }
    });
}());
