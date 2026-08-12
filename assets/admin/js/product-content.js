(function ($) {
    'use strict';

    let editorCounter = 0;
    const config = window.faProductContentAdmin || {};

    function editorSettings() {
        return {
            tinymce: { wpautop: true, branding: false },
            quicktags: true,
            mediaButtons: true
        };
    }

    function createItem(question, answer) {
        const editorId = 'fa_product_faq_answer_' + (++editorCounter);
        const $item = $('<div>', { class: 'fa-product-faq-item' });
        const $header = $('<div>', { class: 'fa-product-faq-header' });
        const $question = $('<input>', {
            type: 'text',
            class: 'widefat fa-product-faq-question',
            placeholder: config.questionPlaceholder || 'متن سؤال…'
        }).val(question || '');
        const $remove = $('<button>', {
            type: 'button',
            class: 'button fa-remove-product-faq',
            text: config.removeLabel || 'حذف'
        });
        const $textarea = $('<textarea>', {
            id: editorId,
            class: 'fa-product-faq-answer',
            rows: 8
        }).val(answer || '');

        $header.append($question, $remove);
        $item.append($header, $('<div>', { class: 'fa-product-faq-answer-wrap' }).append($textarea));

        return $item;
    }

    function initializeEditor($item) {
        const editorId = $item.find('.fa-product-faq-answer').attr('id');

        if (!window.wp || !wp.editor) {
            return;
        }

        window.setTimeout(function () {
            if (window.tinymce && tinymce.get(editorId)) {
                return;
            }

            wp.editor.initialize(editorId, editorSettings());

            if (window.tinymce && tinymce.get(editorId)) {
                tinymce.get(editorId).on('change keyup input', updateJson);
            }
        }, 50);
    }

    function editorContent(editorId) {
        if (window.tinymce) {
            const editor = tinymce.get(editorId);
            if (editor && !editor.isHidden()) {
                return editor.getContent();
            }
        }

        return $('#' + editorId).val() || '';
    }

    function updateJson() {
        if (window.tinymce) {
            tinymce.triggerSave();
        }

        const items = [];
        $('#fa-product-faq-wrapper .fa-product-faq-item').each(function () {
            const $item = $(this);
            const editorId = $item.find('.fa-product-faq-answer').attr('id');
            items.push({
                question: $item.find('.fa-product-faq-question').val() || '',
                answer: editorContent(editorId)
            });
        });
        $('#fa_product_faq').val(JSON.stringify(items));
    }

    $(document).on('click', '#fa-add-product-faq', function () {
        const $item = createItem('', '');
        $('#fa-product-faq-wrapper').append($item);
        initializeEditor($item);
        updateJson();
    });

    $(document).on('click', '.fa-remove-product-faq', function () {
        const $item = $(this).closest('.fa-product-faq-item');
        const editorId = $item.find('.fa-product-faq-answer').attr('id');
        if (window.wp && wp.editor) {
            wp.editor.remove(editorId);
        }
        $item.remove();
        updateJson();
    });

    $(document).on(
        'keyup change',
        '#fa-product-faq-wrapper .fa-product-faq-question, #fa-product-faq-wrapper .fa-product-faq-answer',
        updateJson
    );
    $(document).on('submit', '#post', updateJson);

    $(function () {
        const items = Array.isArray(config.items) ? config.items : [];
        items.forEach(function (item) {
            const $item = createItem(item.question || '', item.answer || '');
            $('#fa-product-faq-wrapper').append($item);
            initializeEditor($item);
        });
        updateJson();
    });
})(jQuery);
