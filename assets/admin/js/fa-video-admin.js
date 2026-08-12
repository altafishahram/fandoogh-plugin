(function ($) {
    'use strict';

    let faVideoFrame = null;

    function faRenderPreview(url, container) {

        if (!url) {
            container.html('');
            return;
        }

        container.html(
            '<video controls preload="metadata" style="max-width:400px;border-radius:8px;">' +
                '<source src="' + url + '">' +
            '</video>'
        );
    }

    $(document).on('click', '.fa-video-upload', function (e) {

        e.preventDefault();

        const wrap = $(this).closest('.fa-video-wrap, td');

        const input = wrap.find('.fa-video-input');

        const preview = wrap.find('.fa-video-preview');

        if (faVideoFrame) {
            faVideoFrame.open();
            return;
        }

        faVideoFrame = wp.media({

            title: 'انتخاب ویدیو',

            button: {
                text: 'انتخاب'
            },

            multiple: false,

            library: {
                type: 'video'
            }

        });

        faVideoFrame.on('select', function () {

            const attachment = faVideoFrame
                .state()
                .get('selection')
                .first()
                .toJSON();

            input.val(attachment.url);

            faRenderPreview(
                attachment.url,
                preview
            );

        });

        faVideoFrame.open();

    });

    $(document).on('click', '.fa-video-remove', function (e) {

        e.preventDefault();

        const wrap = $(this).closest('.fa-video-wrap, td');

        wrap.find('.fa-video-input').val('');

        wrap.find('.fa-video-preview').html('');

    });

    $(document).ready(function () {

        $('.fa-video-input').each(function () {

            const input = $(this);

            const preview = input
                .closest('.fa-video-wrap, td')
                .find('.fa-video-preview');

            faRenderPreview(
                input.val(),
                preview
            );

        });

    });

})(jQuery);