(function ($) {

    'use strict';

    let faEditorCounter = 0;

    function faEditorSettings() {

        return {

            tinymce: {
                wpautop: true,
                branding: false
            },

            quicktags: true,

            mediaButtons: false

        };

    }

    function faCreateItem(question = '', answer = '') {

        const id = 'fa_faq_answer_' + (++faEditorCounter);

        const $item = $(`
            <div class="fa-faq-item">

                <div class="fa-faq-header">

                    <input
                        type="text"
                        class="fa-faq-question"
                        placeholder="متن سوال...">

                    <button
                        type="button"
                        class="button fa-remove-faq">

                        حذف

                    </button>

                </div>

                <div class="fa-faq-answer-wrap">

                    <textarea
                        id="${id}"
                        class="fa-faq-answer"
                        rows="8"></textarea>

                </div>

                <hr>

            </div>
        `);

        // مقداردهی امن: به‌جای تزریق داخل قالب HTML،
        // از .val() استفاده می‌کنیم تا همیشه به‌صورت متن خام نشسته شه
        // (جلوگیری از شکستن ساختار HTML با وجود کوتیشن یا "</textarea>" داخل محتوا)
        $item.find('.fa-faq-question').val(question);
        $item.find('.fa-faq-answer').val(answer);

        return $item;

    }

    function faInitEditor($item) {

        const id = $item
            .find('.fa-faq-answer')
            .attr('id');

        if (
            typeof wp === 'undefined' ||
            typeof wp.editor === 'undefined'
        ) {
            return;
        }

        setTimeout(function () {

            if (
                typeof tinymce !== 'undefined' &&
                tinymce.get(id)
            ) {
                return;
            }

            wp.editor.initialize(
                id,
                faEditorSettings()
            );

            if (typeof tinymce === 'undefined') {
                return;
            }

            const editor = tinymce.get(id);

            if (editor) {

                editor.on(
                    'change keyup input',
                    function () {

                        faUpdateJson();

                    }
                );

            }

        }, 50);

    }

    function faRemoveEditor($item) {

        const id = $item
            .find('.fa-faq-answer')
            .attr('id');

        if (
            typeof wp !== 'undefined' &&
            wp.editor
        ) {

            wp.editor.remove(id);

        }

    }

    function faGetEditorContent(id) {

        if (
            typeof tinymce !== 'undefined'
        ) {

            const editor = tinymce.get(id);

            if (
                editor &&
                !editor.isHidden()
            ) {

                return editor.getContent();

            }

        }

        return $('#' + id).val();

    }
        function faUpdateJson() {

        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        const items = [];

        $('.fa-faq-item').each(function () {

            const $item = $(this);

            const answerId = $item
                .find('.fa-faq-answer')
                .attr('id');

            items.push({

                question: $item
                    .find('.fa-faq-question')
                    .val(),

                answer: faGetEditorContent(answerId)

            });

        });

        $('#fa_faq').val(
            JSON.stringify(items)
        );

    }

    $(document).on(
        'click',
        '#fa-add-faq',
        function () {

            const $item = faCreateItem();

            $('#fa-faq-wrapper')
                .append($item);

            faInitEditor($item);

            faUpdateJson();

        }
    );

    $(document).on(
        'click',
        '.fa-remove-faq',
        function () {

            const $item = $(this)
                .closest('.fa-faq-item');

            faRemoveEditor($item);

            $item.remove();

            faUpdateJson();

        }
    );

    $(document).on(
        'keyup change',
        '.fa-faq-question',
        function () {

            faUpdateJson();

        }
    );

    $(document).on(
        'keyup change',
        '.fa-faq-answer',
        function () {

            faUpdateJson();

        }
    );

    $(document).on(
        'submit',
        'form',
        function () {

            faUpdateJson();

        }
    );

    $(function () {

        // ساخت فیلد مخفی برای ارسال JSON سوالات/پاسخ‌ها همراه فرم.
        // به‌جای appendTo('form') که می‌تواند فرم اشتباه را هدف بگیرد
        // (در صفحات وردپرس معمولاً بیش از یک <form> وجود دارد،
        // مثل فرم فیلتر/جستجوی لیست دسته‌ها)، نزدیک‌ترین فرم به
        // باکس FAQ را پیدا می‌کنیم.
        if ($('#fa_faq').length === 0) {

            $('<input>', {

                type: 'hidden',

                id: 'fa_faq',

                name: 'fa_faq'

            }).appendTo(
                $('#fa-faq-wrapper').closest('form')
            );

        }

        if (
            typeof window.faFaqData !== 'undefined' &&
            Array.isArray(window.faFaqData)
        ) {

            window.faFaqData.forEach(function (item) {

                const $item = faCreateItem(
                    item.question || '',
                    item.answer || ''
                );

                $('#fa-faq-wrapper')
                    .append($item);

                faInitEditor($item);

            });

        }

        faUpdateJson();

    });

})(jQuery);