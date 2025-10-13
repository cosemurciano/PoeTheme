(function () {
    'use strict';

    var TARGET_SELECTOR = '.entry-content, .gallery, .wp-block-gallery';
    var LIGHTBOX_CLASS = 'poetheme-lightbox';
    var ACTIVE_CLASS = 'is-active';
    var BODY_OPEN_CLASS = 'poetheme-lightbox-open';
    var TARGET_WIDTH = 1024;

    var lastActiveElement = null;
    var lightboxEl;
    var lightboxImage;
    var closeButton;

    function createLightbox() {
        if (lightboxEl) {
            return;
        }

        lightboxEl = document.createElement('div');
        lightboxEl.className = LIGHTBOX_CLASS;
        lightboxEl.setAttribute('role', 'dialog');
        lightboxEl.setAttribute('aria-modal', 'true');
        lightboxEl.setAttribute('aria-label', 'Anteprima immagine');
        lightboxEl.innerHTML = '' +
            '<div class="poetheme-lightbox__content" tabindex="-1">' +
                '<button type="button" class="poetheme-lightbox__close" aria-label="Chiudi">&times;</button>' +
                '<img class="poetheme-lightbox__image" alt="" loading="lazy" />' +
            '</div>';

        document.body.appendChild(lightboxEl);

        lightboxImage = lightboxEl.querySelector('.poetheme-lightbox__image');
        closeButton = lightboxEl.querySelector('.poetheme-lightbox__close');

        closeButton.addEventListener('click', closeLightbox);
        lightboxEl.addEventListener('click', function (event) {
            if (event.target === lightboxEl) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!lightboxEl.classList.contains(ACTIVE_CLASS)) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightbox();
            }
        });
    }

    function getClosestContentImage(element) {
        if (!element) {
            return null;
        }

        var scope = element.closest(TARGET_SELECTOR);
        if (!scope) {
            return null;
        }

        return element.closest('img');
    }

    function resolveSrcFromSrcset(image) {
        var srcset = image.getAttribute('srcset');

        if (!srcset) {
            return '';
        }

        var sources = [];

        srcset.split(',').forEach(function (item) {
            var trimmed = item.trim();

            if (!trimmed) {
                return;
            }

            var parts = trimmed.split(/\s+/);
            var url = parts[0];
            var descriptor = parts[1] || '';
            var width = 0;

            if (descriptor.endsWith('w')) {
                width = parseInt(descriptor, 10);
            } else if (descriptor.endsWith('x')) {
                var multiplier = parseFloat(descriptor);
                if (!isNaN(multiplier) && image.naturalWidth) {
                    width = image.naturalWidth * multiplier;
                }
            }

            sources.push({ url: url, width: width });
        });

        if (!sources.length) {
            return '';
        }

        sources.sort(function (a, b) {
            return a.width - b.width;
        });

        for (var i = 0; i < sources.length; i++) {
            if (sources[i].width >= TARGET_WIDTH && sources[i].width !== 0) {
                return sources[i].url;
            }
        }

        return sources[sources.length - 1].url;
    }

    function resolveLightboxUrl(image, anchor) {
        if (!image) {
            return '';
        }

        var dataset = image.dataset || {};

        if (dataset.poethemeLightbox) {
            return dataset.poethemeLightbox;
        }

        if (dataset.fullUrl) {
            return dataset.fullUrl;
        }

        if (dataset.largeFile) {
            return dataset.largeFile;
        }

        if (dataset.original) {
            return dataset.original;
        }

        if (dataset.originalFile) {
            return dataset.originalFile;
        }

        var srcFromSet = resolveSrcFromSrcset(image);
        if (srcFromSet) {
            return srcFromSet;
        }

        if (anchor && anchor.href) {
            return anchor.href;
        }

        if (image.currentSrc) {
            return image.currentSrc;
        }

        return image.src;
    }

    function isImageLink(url) {
        if (!url) {
            return false;
        }

        try {
            var parsed = new URL(url, window.location.href);
            url = parsed.pathname;
        } catch (e) {
            // Ignore parsing errors and use raw URL.
        }

        return /\.(?:jpe?g|png|gif|bmp|webp|avif|svg)$/i.test(url);
    }

    function openLightbox(url, altText) {
        if (!url) {
            return;
        }

        createLightbox();

        lastActiveElement = document.activeElement;

        lightboxImage.src = url;
        lightboxImage.alt = altText || '';
        lightboxEl.classList.add(ACTIVE_CLASS);
        document.body.classList.add(BODY_OPEN_CLASS);

        var focusTarget = lightboxEl.querySelector('.poetheme-lightbox__content');
        if (focusTarget) {
            focusTarget.focus();
        }
    }

    function closeLightbox() {
        if (!lightboxEl || !lightboxEl.classList.contains(ACTIVE_CLASS)) {
            return;
        }

        lightboxEl.classList.remove(ACTIVE_CLASS);
        document.body.classList.remove(BODY_OPEN_CLASS);

        if (lightboxImage) {
            lightboxImage.removeAttribute('src');
            lightboxImage.removeAttribute('alt');
        }

        if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
            lastActiveElement.focus();
        }

        lastActiveElement = null;
    }

    document.addEventListener('click', function (event) {
        var clickedImage = getClosestContentImage(event.target);

        if (!clickedImage) {
            return;
        }

        var anchor = event.target.closest('a');
        var lightboxUrl = resolveLightboxUrl(clickedImage, anchor);

        if (!lightboxUrl) {
            return;
        }

        var hasMeaningfulSource = clickedImage.hasAttribute('srcset') || clickedImage.dataset.fullUrl || clickedImage.dataset.largeFile || clickedImage.dataset.original || clickedImage.dataset.originalFile;

        if (anchor && lightboxUrl === anchor.href && !hasMeaningfulSource && !isImageLink(lightboxUrl)) {
            return;
        }

        if (!anchor && !hasMeaningfulSource) {
            if (!isImageLink(lightboxUrl)) {
                return;
            }
        }

        event.preventDefault();
        openLightbox(lightboxUrl, clickedImage.alt);
    });
})();
