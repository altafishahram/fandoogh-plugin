(function ($) {
    'use strict';

    let videoFrame = null;
    let galleryFrame = null;
    const $videoInput = $('#fa_customer_video');
    const $videoPreview = $('#fa-video-preview');
    const $galleryInput = $('#fa_customer_gallery');
    const $galleryPreview = $('#fa-gallery-preview');

    function renderVideo(url) {
        $videoPreview.empty();

        if (url) {
            $('<video>', {controls: true, class: 'fa-video-preview-player'})
                .append($('<source>', {src: url}))
                .appendTo($videoPreview);
        }
    }

    $(document).on('click', '#fa-upload-video', function (event) {
        event.preventDefault();

        if (!videoFrame) {
            videoFrame = wp.media({
                title: 'انتخاب ویدئو',
                button: {text: 'انتخاب'},
                library: {type: 'video'},
                multiple: false
            });
            videoFrame.on('select', function () {
                const attachment = videoFrame.state().get('selection').first().toJSON();
                $videoInput.val(attachment.url);
                renderVideo(attachment.url);
            });
        }

        videoFrame.open();
    });

    $(document).on('click', '#fa-remove-video', function () {
        $videoInput.val('');
        renderVideo('');
    });

    $(document).on('click', '#fa-upload-gallery', function (event) {
        event.preventDefault();

        if (!galleryFrame) {
            galleryFrame = wp.media({
                title: 'انتخاب تصاویر',
                button: {text: 'ثبت تصاویر'},
                library: {type: 'image'},
                multiple: true
            });
            galleryFrame.on('select', function () {
                const ids = [];
                $galleryPreview.empty();
                galleryFrame.state().get('selection').each(function (model) {
                    const image = model.toJSON();
                    const url = image.sizes && image.sizes.thumbnail
                        ? image.sizes.thumbnail.url
                        : image.url;
                    ids.push(image.id);
                    $('<div>', {class: 'fa-gallery-item'})
                        .append($('<img>', {src: url, alt: ''}))
                        .appendTo($galleryPreview);
                });
                $galleryInput.val(ids.join(','));
            });
        }

        galleryFrame.open();
    });

    $(document).on('click', '#fa-remove-gallery', function () {
        $galleryInput.val('');
        $galleryPreview.empty();
    });
})(jQuery);
