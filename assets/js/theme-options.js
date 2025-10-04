(function ($) {
    'use strict';

    $(function () {
        var frame;
        var $logoId = $('#poetheme_logo_id');
        var $preview = $('#poetheme-logo-preview');
        var $removeButton = $('#poetheme-logo-remove');
        var $titleToggle = $('#poetheme_logo_show_site_title');
        var $logoOptions = $('.poetheme-logo-options');
        var $titleOptions = $('.poetheme-title-options');
        var $logoHeight = $('#poetheme_logo_height');
        var $titleColor = $('#poetheme_logo_title_color');
        var $titleSize = $('#poetheme_logo_title_size');
        var $imageWrapper = $preview.find('.poetheme-logo-preview__image-wrapper');
        var $titleWrapper = $preview.find('.poetheme-logo-preview__title-wrapper');

        function toggleMode() {
            var showTitle = $titleToggle.is(':checked');
            $titleOptions.toggle(showTitle);
            $logoOptions.toggle(!showTitle);

            if (showTitle) {
                $imageWrapper.hide();
                $titleWrapper.show();
            } else {
                $titleWrapper.hide();
                $imageWrapper.show();
            }
        }

        function applyLogoHeight() {
            var height = parseInt($logoHeight.val(), 10);
            var $img = $imageWrapper.find('img');

            if (!$img.length) {
                return;
            }

            if (height > 0) {
                $img.css({
                    height: height + 'px',
                    width: 'auto'
                });
            } else {
                $img.css({
                    height: '',
                    width: ''
                });
            }
        }

        function applyTitleStyles() {
            var color = $titleColor.val() || '#111827';
            var size = parseInt($titleSize.val(), 10);
            var $title = $titleWrapper.find('.poetheme-logo-preview__title');
            var $tagline = $titleWrapper.find('.poetheme-logo-preview__tagline');

            $title.css('color', color);

            if (size > 0) {
                $title.css('font-size', size + 'px');
            } else {
                $title.css('font-size', '');
            }

            if ($tagline.length) {
                $tagline.css({
                    color: color,
                    opacity: 0.75
                });
            }
        }

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
                $imageWrapper.html('<img src="' + imageUrl + '" alt="" class="poetheme-logo-preview__image" />');
                $removeButton.prop('disabled', false);
                applyLogoHeight();
                toggleMode();
            });

            frame.open();
        });

        $removeButton.on('click', function (event) {
            event.preventDefault();

            $logoId.val('');
            $imageWrapper.html('<p class="description">' + poethemeThemeOptions.noLogo + '</p>');
            $removeButton.prop('disabled', true);
            toggleMode();
        });

        $titleToggle.on('change', toggleMode);
        $logoHeight.on('input change', applyLogoHeight);
        $titleColor.on('input change', applyTitleStyles);
        $titleSize.on('input change', applyTitleStyles);

        toggleMode();
        applyLogoHeight();
        applyTitleStyles();
    });
})(jQuery);
