(function ($) {
    'use strict';

    $(function () {
        var frame;
        var backgroundFrame;
        var options = typeof poethemeThemeOptions !== 'undefined' ? poethemeThemeOptions : {};
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
        var $backgroundId = $('#poetheme_global_background_image_id');
        var $backgroundPreview = $('#poetheme-background-preview');
        var $backgroundUpload = $('#poetheme-background-upload');
        var $backgroundRemove = $('#poetheme-background-remove');
        var $colorFields = $('.poetheme-color-field');
        var $nativeColorInputs = $('.poetheme-color-native');
        var $clearColorButtons = $('.poetheme-color-clear');

        function toggleMode() {
            if (!$titleToggle.length) {
                return;
            }

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
            if (!$logoHeight.length) {
                return;
            }

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
            if (!$titleColor.length && !$titleSize.length) {
                return;
            }

            var color = $titleColor.val() || '#111827';
            var size = parseInt($titleSize.val(), 10);
            var $title = $titleWrapper.find('.poetheme-logo-preview__title');
            var $tagline = $titleWrapper.find('.poetheme-logo-preview__tagline');

            if ($title.length) {
                $title.css('color', color);
                if (size > 0) {
                    $title.css('font-size', size + 'px');
                } else {
                    $title.css('font-size', '');
                }
            }

            if ($tagline.length) {
                $tagline.css({
                    color: color,
                    opacity: 0.75
                });
            }
        }

        if ($colorFields.length && $.fn.wpColorPicker) {
            $colorFields.each(function () {
                var $field = $(this);
                var defaultColor = $field.data('default-color');
                var $wrapper = $field.closest('.poetheme-color-control');
                var $native = $wrapper.find('.poetheme-color-native');

                function setNativeEmptyState(isEmpty) {
                    if (!$native.length) {
                        return;
                    }

                    var nativeDefault = $native.data('default-color');
                    nativeDefault = nativeDefault || '#000000';

                    if (isEmpty) {
                        $native.addClass('poetheme-color-native--empty');
                        $native.val(nativeDefault);
                    } else {
                        $native.removeClass('poetheme-color-native--empty');
                    }
                }

                if ($native.length) {
                    if ($field.val()) {
                        $native.val($field.val());
                        $native.removeClass('poetheme-color-native--empty');
                    } else {
                        setNativeEmptyState(true);
                    }
                }

                $field.wpColorPicker({
                    defaultColor: defaultColor || false,
                    change: function (event, ui) {
                        if (ui && ui.color) {
                            var colorValue = ui.color.toString();
                            $field.val(colorValue);
                            if ($native.length) {
                                $native.val(colorValue);
                                $native.removeClass('poetheme-color-native--empty');
                            }
                        }
                    },
                    clear: function () {
                        $field.val('');
                        setNativeEmptyState(true);
                    }
                });

                $field.on('input change', function () {
                    var value = $field.val();
                    if (!$native.length) {
                        return;
                    }

                    if (value) {
                        $native.val(value);
                        $native.removeClass('poetheme-color-native--empty');
                    } else {
                        setNativeEmptyState(true);
                    }
                });
            });
        }

        if ($nativeColorInputs.length) {
            $nativeColorInputs.on('input change', function () {
                var $native = $(this);
                var $wrapper = $native.closest('.poetheme-color-control');
                var $field = $wrapper.find('.poetheme-color-field');
                var color = $native.val();

                if ($field.length) {
                    if (typeof $field.wpColorPicker === 'function') {
                        $field.wpColorPicker('color', color);
                    }
                    $field.val(color).trigger('change');
                }

                $native.removeClass('poetheme-color-native--empty');
            });
        }

        if ($clearColorButtons.length) {
            $clearColorButtons.on('click', function (event) {
                event.preventDefault();

                var $button = $(this);
                var targetId = $button.data('target');
                var $field = $('#' + targetId);

                if (!$field.length) {
                    return;
                }

                if (typeof $field.wpColorPicker === 'function') {
                    $field.wpColorPicker('color', '');
                }

                $field.val('').trigger('change');

                var $wrapper = $field.closest('.poetheme-color-control');
                var $native = $wrapper.find('.poetheme-color-native');
                if ($native.length) {
                    var nativeDefault = $native.data('default-color');
                    $native.val(nativeDefault || '#000000');
                    $native.addClass('poetheme-color-native--empty');
                }
            });
        }

        $('#poetheme-logo-upload').on('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: options.chooseLogo || '',
                button: {
                    text: options.selectLogo || ''
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
            $imageWrapper.html('<p class="description">' + (options.noLogo || '') + '</p>');
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

        if ($backgroundId.length && $backgroundUpload.length) {
            $backgroundUpload.on('click', function (event) {
                event.preventDefault();

                if (backgroundFrame) {
                    backgroundFrame.open();
                    return;
                }

                backgroundFrame = wp.media({
                    title: options.chooseBackground || '',
                    button: {
                        text: options.selectBackground || ''
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });

                backgroundFrame.on('select', function () {
                    var attachment = backgroundFrame.state().get('selection').first().toJSON();
                    var imageUrl = attachment.url;

                    if (attachment.sizes && attachment.sizes.large) {
                        imageUrl = attachment.sizes.large.url;
                    }

                    $backgroundId.val(attachment.id);
                    $backgroundPreview.html('<img src="' + imageUrl + '" alt="" />');
                    $backgroundRemove.prop('disabled', false);
                });

                backgroundFrame.open();
            });
        }

        if ($backgroundRemove.length) {
            $backgroundRemove.on('click', function (event) {
                event.preventDefault();

                $backgroundId.val('');
                if ($backgroundPreview.length) {
                    var emptyLabel = options.noBackground || '';
                    $backgroundPreview.html('<p class="description">' + emptyLabel + '</p>');
                }
                $backgroundRemove.prop('disabled', true);
            });
        }
    });
})(jQuery);
