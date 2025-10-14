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
    var lightboxContent;
    var galleryItems = [];
    var currentIndex = -1;
    var touchStartX = 0;
    var touchStartY = 0;
    var touchInProgress = false;
    var SWIPE_THRESHOLD = 40;

    function setLightboxImage(url, altText) {
        if (!lightboxImage || !url) {
            return;
        }

        lightboxImage.src = url;
        lightboxImage.alt = altText || '';
    }

    function setGalleryContext(items, index) {
        if (!Array.isArray(items) || !items.length) {
            galleryItems = [];
            currentIndex = -1;
            return;
        }

        galleryItems = items;

        if (typeof index !== 'number' || index < 0 || index >= galleryItems.length) {
            currentIndex = 0;
        } else {
            currentIndex = index;
        }
    }

    function resetGalleryContext() {
        galleryItems = [];
        currentIndex = -1;
    }

    function showImageAtIndex(index) {
        if (!lightboxEl || !lightboxEl.classList.contains(ACTIVE_CLASS) || !galleryItems.length) {
            return;
        }

        if (index < 0) {
            index = galleryItems.length - 1;
        } else if (index >= galleryItems.length) {
            index = 0;
        }

        currentIndex = index;

        var item = galleryItems[index];

        if (!item || !item.url) {
            return;
        }

        setLightboxImage(item.url, item.alt);

        if (lightboxContent && document.activeElement !== lightboxContent) {
            lightboxContent.focus({ preventScroll: true });
        }
    }

    function navigateLightbox(step) {
        if (!galleryItems || galleryItems.length < 2) {
            return false;
        }

        var nextIndex = currentIndex + step;

        if (nextIndex < 0) {
            nextIndex = galleryItems.length - 1;
        } else if (nextIndex >= galleryItems.length) {
            nextIndex = 0;
        }

        showImageAtIndex(nextIndex);

        return true;
    }

    function handleTouchStart(event) {
        if (!lightboxEl || !lightboxEl.classList.contains(ACTIVE_CLASS)) {
            return;
        }

        if (!event.touches || event.touches.length !== 1) {
            return;
        }

        var touch = event.touches[0];
        touchStartX = touch.clientX;
        touchStartY = touch.clientY;
        touchInProgress = true;
    }

    function handleTouchCancel() {
        if (!touchInProgress) {
            return;
        }

        touchInProgress = false;
        touchStartX = 0;
        touchStartY = 0;
    }

    function handleTouchEnd(event) {
        if (!touchInProgress) {
            return;
        }

        touchInProgress = false;

        if (!event.changedTouches || event.changedTouches.length !== 1) {
            return;
        }

        var touch = event.changedTouches[0];
        var deltaX = touch.clientX - touchStartX;
        var deltaY = touch.clientY - touchStartY;

        touchStartX = 0;
        touchStartY = 0;

        if (Math.abs(deltaX) <= Math.abs(deltaY)) {
            return;
        }

        if (Math.abs(deltaX) < SWIPE_THRESHOLD) {
            return;
        }

        if (deltaX < 0) {
            navigateLightbox(1);
        } else if (deltaX > 0) {
            navigateLightbox(-1);
        }
    }

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
        lightboxContent = lightboxEl.querySelector('.poetheme-lightbox__content');

        closeButton.addEventListener('click', closeLightbox);
        lightboxEl.addEventListener('click', function (event) {
            if (event.target === lightboxEl) {
                closeLightbox();
            }
        });

        if (lightboxContent) {
            lightboxContent.addEventListener('touchstart', handleTouchStart, { passive: true });
            lightboxContent.addEventListener('touchend', handleTouchEnd, { passive: true });
            lightboxContent.addEventListener('touchcancel', handleTouchCancel, { passive: true });
        }

        document.addEventListener('keydown', function (event) {
            if (!lightboxEl.classList.contains(ACTIVE_CLASS)) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightbox();
                return;
            }

            if (event.key === 'ArrowRight') {
                if (navigateLightbox(1)) {
                    event.preventDefault();
                }
                return;
            }

            if (event.key === 'ArrowLeft') {
                if (navigateLightbox(-1)) {
                    event.preventDefault();
                }
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

    function buildGalleryItems(image, resolvedUrl) {
        var container = image ? image.closest('.gallery, .wp-block-gallery') : null;
        var items = [];
        var index = -1;

        if (container) {
            var scopeImages = Array.prototype.slice.call(container.querySelectorAll('img'));

            scopeImages.forEach(function (galleryImage) {
                var anchor = galleryImage.closest('a');
                var url = resolveLightboxUrl(galleryImage, anchor);

                if (!url) {
                    return;
                }

                if (galleryImage === image) {
                    index = items.length;
                }

                items.push({
                    element: galleryImage,
                    url: url,
                    alt: galleryImage.alt || ''
                });
            });
        }

        if (!items.length) {
            items.push({
                element: image,
                url: resolvedUrl,
                alt: image ? image.alt || '' : ''
            });
            index = 0;
        } else if (index === -1) {
            items.push({
                element: image,
                url: resolvedUrl,
                alt: image ? image.alt || '' : ''
            });
            index = items.length - 1;
        }

        return {
            items: items,
            index: index
        };
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

        var wasActive = lightboxEl.classList.contains(ACTIVE_CLASS);

        if (!wasActive) {
            lastActiveElement = document.activeElement;
        }

        setLightboxImage(url, altText);

        lightboxEl.classList.add(ACTIVE_CLASS);
        document.body.classList.add(BODY_OPEN_CLASS);

        if (!wasActive && lightboxContent) {
            lightboxContent.focus();
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
        resetGalleryContext();
        touchInProgress = false;
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

        var galleryData = buildGalleryItems(clickedImage, lightboxUrl);
        setGalleryContext(galleryData.items, galleryData.index);

        var initialItem = (galleryItems.length && currentIndex > -1) ? galleryItems[currentIndex] : {
            url: lightboxUrl,
            alt: clickedImage.alt || ''
        };

        openLightbox(initialItem.url, initialItem.alt);
    });
})();
