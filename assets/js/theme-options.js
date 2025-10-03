(function ($) {
    'use strict';

    $(function () {
        var frame;
        var $logoId = $('#poetheme_logo_id');
        var $preview = $('#poetheme-logo-preview');
        var $removeButton = $('#poetheme-logo-remove');

        $('#poetheme-logo-upload').on('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: poethemeThemeOptions.chooseLogo,
                button: {
                    text: poethemeThemeOptions.selectLogo
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                var imageUrl = attachment.url;

                if (attachment.sizes && attachment.sizes.medium) {
                    imageUrl = attachment.sizes.medium.url;
                }

                $logoId.val(attachment.id);
                $preview.html('<img src="' + imageUrl + '" alt="" class="poetheme-logo-preview__image" />');
                $removeButton.prop('disabled', false);
            });

            frame.open();
        });

        $removeButton.on('click', function (event) {
            event.preventDefault();

            $logoId.val('');
            $preview.html('<p class="description">' + poethemeThemeOptions.noLogo + '</p>');
            $removeButton.prop('disabled', true);
        });
    });
})(jQuery);
