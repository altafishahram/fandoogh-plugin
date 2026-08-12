(function ($) {

    'use strict';

    let videoFrame = null;
    let galleryFrame = null;

    /*
    -------------------------
    Video Upload
    -------------------------
    */

    $(document).on(
        'click',
        '#fa-upload-video',
        function (e) {

            e.preventDefault();

            if (videoFrame) {
                videoFrame.open();
                return;
            }

            videoFrame = wp.media({

                title: 'انتخاب ویدئو',

                button: {
                    text: 'انتخاب'
                },

                library: {
                    type: 'video'
                },

                multiple: false

            });

            videoFrame.on('select', function () {

                const attachment = videoFrame
                    .state()
                    .get('selection')
                    .first()
                    .toJSON();

                $('#fa_project_video').val(
                    attachment.url
                );

                $('#fa-video-preview').html(

                    '<video controls>' +

                        '<source src="' +

                        attachment.url +

                        '">' +

                    '</video>'

                );

            });

            videoFrame.open();

        }

    );

    /*
    -------------------------
    Remove Video
    -------------------------
    */

    $(document).on(
        'click',
        '#fa-remove-video',
        function () {

            $('#fa_project_video').val('');

            $('#fa-video-preview').empty();

        }

    );

    /*
    -------------------------
    Gallery Upload
    -------------------------
    */

    $(document).on(
        'click',
        '#fa-upload-gallery',
        function (e) {

            e.preventDefault();

            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            galleryFrame = wp.media({

                title: 'انتخاب تصاویر',

                button: {
                    text: 'ثبت تصاویر'
                },

                library: {
                    type: 'image'
                },

                multiple: true

            });

            galleryFrame.on(
                'select',
                function () {

                    const selection = galleryFrame
                        .state()
                        .get('selection');

                    let ids = [];

                    let html = '';

                    selection.each(function (attachment) {

                        attachment = attachment.toJSON();

                        ids.push(
                            attachment.id
                        );

                        const image =

                            attachment.sizes &&
                            attachment.sizes.thumbnail

                            ?

                            attachment.sizes.thumbnail.url

                            :

                            attachment.url;

                        html +=

                            '<div class="fa-gallery-item">' +

                                '<img src="' +

                                image +

                                '">' +

                            '</div>';

                    });
                                        $('#fa_project_gallery').val(
                        ids.join(',')
                    );

                    $('#fa-gallery-preview').html(
                        html
                    );

                }

            );

            galleryFrame.open();

        }

    );

    /*
    -------------------------
    Remove Gallery
    -------------------------
    */

    $(document).on(
        'click',
        '#fa-remove-gallery',
        function () {

            $('#fa_project_gallery').val('');

            $('#fa-gallery-preview').empty();

        }

    );

    $(function () {
        const fields = {
            'fa_project_contractor': '[fa_project_contractor]',
            'fa_project_excerpt': '[fa_project_description]',
            'fa_project_address': '[fa_project_address]',
            'fa_project_categories[]': '[fa_project_product_categories]',
            'fa_project_video': '[fa_project_video]',
            'fa_project_gallery': '[fa_project_gallery]'
        };

        Object.keys(fields).forEach(function (name) {
            const $field = $('[name="' + name + '"]').first();
            const $heading = $field.closest('tr').find('th').first();
            if ($heading.length) {
                $('<code>', {class: 'fa-field-shortcode', text: fields[name]}).appendTo($heading);
            }
        });

        $('.fa-card-header').first().append(
            '<div class="fa-project-shortcode-reference">' +
            '<span>نام: <code>[fa_project_name]</code></span> ' +
            '<span>تصویر: <code>[fa_project_image]</code></span> ' +
            '<span>دسته‌بندی پروژه: <code>[fa_project_categories]</code></span>' +
            '</div>'
        );
    });

})(jQuery);
