'use strict';

class FaReviews {
    constructor() {
        this.form = document.getElementById('fa-review-form');

        if (!this.form) {
            return;
        }

        this.notice = document.getElementById('fa-review-notice');
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    async handleSubmit(event) {
        event.preventDefault();
        this.clearNotice();

        const submitButton = this.form.querySelector('[type="submit"]');
        const formData = new FormData(this.form);
        formData.append('action', 'fa_submit_review');

        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            const response = await fetch(faReviews.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (result.success) {
                this.showNotice(result.data.message, 'success');
                this.form.reset();
                return;
            }

            this.showNotice(
                result.data && result.data.message
                    ? result.data.message
                    : 'ثبت نظر انجام نشد.',
                'error'
            );
        } catch (error) {
            this.showNotice('در ارتباط با سرور خطایی رخ داد.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    }

    showNotice(message, type = 'success') {
        if (!this.notice) {
            return;
        }

        this.notice.textContent = message;
        this.notice.className =
            'fa-review-notice fa-review-notice--' + type;
    }

    clearNotice() {
        if (!this.notice) {
            return;
        }

        this.notice.textContent = '';
        this.notice.className = 'fa-review-notice';
    }
}

document.addEventListener('DOMContentLoaded', () => new FaReviews());
