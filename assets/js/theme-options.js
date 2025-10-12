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
        var alphaLabel = options.alphaLabel || 'Transparency';
        var alphaSuffix = options.alphaSuffix || '%';

        function clamp(value, min, max) {
            value = Number(value);
            if (Number.isNaN(value)) {
                return min;
            }
            return Math.min(Math.max(value, min), max);
        }

        function componentToHex(component) {
            var hex = clamp(component, 0, 255).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }

        function rgbToHex(r, g, b) {
            return '#' + componentToHex(r) + componentToHex(g) + componentToHex(b);
        }

        function parseColor(value, fallback) {
            var state = {
                r: 17,
                g: 24,
                b: 39,
                a: 1,
                format: 'hex'
            };

            if (fallback && typeof fallback === 'object') {
                state = $.extend({}, state, fallback);
            }

            var stringValue = (value || '').toString().trim();

            if (!stringValue) {
                return state;
            }

            if (stringValue.toLowerCase() === 'transparent') {
                state.a = 0;
                state.format = 'rgba';
                return state;
            }

            if (typeof window.Color !== 'undefined') {
                try {
                    var colorObj = window.Color(stringValue);
                    if (colorObj && colorObj.toRgb) {
                        var rgb = colorObj.toRgb();
                        if (rgb) {
                            state.r = clamp(rgb.r, 0, 255);
                            state.g = clamp(rgb.g, 0, 255);
                            state.b = clamp(rgb.b, 0, 255);
                            state.a = typeof rgb.a === 'number' ? clamp(rgb.a, 0, 1) : 1;
                            state.format = state.a < 1 ? 'rgba' : (stringValue.indexOf('#') === 0 ? 'hex' : 'rgb');
                            return state;
                        }
                    }
                } catch (e) {
                    // Fallback to manual parsing.
                }
            }

            var hexMatch = stringValue.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i);
            if (hexMatch) {
                var hex = hexMatch[1];
                if (hex.length === 3) {
                    state.r = parseInt(hex.charAt(0) + hex.charAt(0), 16);
                    state.g = parseInt(hex.charAt(1) + hex.charAt(1), 16);
                    state.b = parseInt(hex.charAt(2) + hex.charAt(2), 16);
                } else {
                    state.r = parseInt(hex.substr(0, 2), 16);
                    state.g = parseInt(hex.substr(2, 2), 16);
                    state.b = parseInt(hex.substr(4, 2), 16);
                }
                state.format = 'hex';
                return state;
            }

            var rgbaMatch = stringValue.match(/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/i);
            if (rgbaMatch) {
                state.r = clamp(parseInt(rgbaMatch[1], 10), 0, 255);
                state.g = clamp(parseInt(rgbaMatch[2], 10), 0, 255);
                state.b = clamp(parseInt(rgbaMatch[3], 10), 0, 255);
                state.a = rgbaMatch[4] !== undefined ? clamp(parseFloat(rgbaMatch[4]), 0, 1) : 1;
                state.format = state.a < 1 ? 'rgba' : 'rgb';
                return state;
            }

            return state;
        }

        function formatColor(state) {
            var alpha = clamp(state.a, 0, 1);
            if (alpha < 1) {
                var alphaString = alpha === 0 || alpha === 1 ? String(alpha) : alpha.toFixed(2).replace(/0+$/, '').replace(/\.$/, '');
                return 'rgba(' + clamp(state.r, 0, 255) + ',' + clamp(state.g, 0, 255) + ',' + clamp(state.b, 0, 255) + ',' + alphaString + ')';
            }

            return rgbToHex(state.r, state.g, state.b);
        }

        function updateAlphaDisplay($field, state) {
            var $slider = $field.data('poethemeAlphaSlider');
            var $value = $field.data('poethemeAlphaValue');

            if (!$slider || !$slider.length) {
                return;
            }

            var percentage = Math.round(clamp(state.a, 0, 1) * 100);
            $slider.val(percentage);

            if ($value && $value.length) {
                $value.text(percentage + alphaSuffix);
            }
        }

        function updateColorPreview($field) {
            if (!$field || !$field.length) {
                return;
            }

            var fieldId = $field.attr('id');

            if (!fieldId) {
                return;
            }

            var $preview = $('[data-preview-for="' + fieldId + '"]');

            if (!$preview.length) {
                return;
            }

            var colorValue = $field.val() || $field.data('default-color') || 'transparent';

            $preview.each(function () {
                this.style.setProperty('--poetheme-preview-color', colorValue || 'transparent');
            });
        }

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

            var defaultColor = $titleColor.data('default-color') || '#111827';
            var color = $titleColor.val() || defaultColor;
            var size = parseFloat($titleSize.val());
            var $title = $titleWrapper.find('.poetheme-logo-preview__title');
            var $tagline = $titleWrapper.find('.poetheme-logo-preview__tagline');

            if ($title.length) {
                $title.css('color', color);
                if (Number.isFinite(size) && size > 0) {
                    $title.css('font-size', size + 'rem');
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
                var supportsAlpha = $field.data('supports-alpha');
                var initialState = parseColor($field.val() || defaultColor || '', null);

                $field.wpColorPicker({
                    defaultColor: defaultColor || false,
                    change: function (event, ui) {
                        if (ui && ui.color) {
                            var $target = $(event.target);
                            var currentState = parseColor(ui.color.toString(), $target.data('poethemeColorState'));
                            var hasAlpha = !!$target.data('poethemeSupportsAlpha');
                            if (hasAlpha) {
                                currentState.a = ($target.data('poethemeColorState') || currentState).a;
                            }
                            $target.data('poethemeColorState', currentState);
                            var formatted = hasAlpha ? formatColor(currentState) : ui.color.toString();
                            $target.val(formatted).trigger('change');
                            if (hasAlpha) {
                                updateAlphaDisplay($target, currentState);
                            }
                            updateColorPreview($target);
                        }
                    },
                    clear: function (event) {
                        var $target = $(event.target);
                        $target.val('').trigger('change');
                        if ($target.data('poethemeSupportsAlpha')) {
                            var defaultState = parseColor($target.data('default-color') || '', null);
                            $target.data('poethemeColorState', defaultState);
                            updateAlphaDisplay($target, defaultState);
                        }
                        updateColorPreview($target);
                    }
                });

                $field.data('poethemeColorState', initialState);
                $field.data('poethemeSupportsAlpha', !!supportsAlpha);

                if (supportsAlpha) {
                    var $container = $field.closest('.wp-picker-container');
                    var $alphaControl = $('<div class="poetheme-alpha-control"></div>');
                    var $alphaLabel = $('<span class="poetheme-alpha-label"></span>').text(alphaLabel);
                    var $alphaSliderWrap = $('<div class="poetheme-alpha-slider-wrap"></div>');
                    var $alphaSlider = $('<input type="range" min="0" max="100" step="1" class="poetheme-alpha-slider" />');
                    var $alphaValue = $('<span class="poetheme-alpha-value"></span>');

                    $alphaSliderWrap.append($alphaSlider);
                    $alphaSliderWrap.append($alphaValue);
                    $alphaControl.append($alphaLabel);
                    $alphaControl.append($alphaSliderWrap);

                    if ($container.length) {
                        $container.append($alphaControl);
                    } else {
                        $field.after($alphaControl);
                    }

                    $field.data('poethemeAlphaSlider', $alphaSlider);
                    $field.data('poethemeAlphaValue', $alphaValue);

                    $alphaSlider.on('input change', function () {
                        var sliderValue = clamp(parseInt(this.value, 10), 0, 100) / 100;
                        var state = $field.data('poethemeColorState') || initialState;
                        state = $.extend({}, state, { a: sliderValue, format: sliderValue < 1 ? 'rgba' : state.format });
                        $field.data('poethemeColorState', state);
                        var formatted = formatColor(state);
                        $field.val(formatted);
                        updateAlphaDisplay($field, state);
                        updateColorPreview($field);
                    });

                    updateAlphaDisplay($field, initialState);
                }

                $field.on('input change', function () {
                    var state = parseColor($field.val(), $field.data('poethemeColorState'));
                    $field.data('poethemeColorState', state);
                    if ($field.data('poethemeSupportsAlpha')) {
                        updateAlphaDisplay($field, state);
                    }
                    updateColorPreview($field);
                });

                updateColorPreview($field);
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

                $backgroundId.val('0');
                if ($backgroundPreview.length) {
                    var emptyLabel = options.noBackground || '';
                    $backgroundPreview.html('<p class="description">' + emptyLabel + '</p>');
                }
                $backgroundRemove.prop('disabled', true);
            });
        }
    });
})(jQuery);
