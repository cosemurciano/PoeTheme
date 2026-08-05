(function () {
    'use strict';

    var TARGET_SELECTOR = '.entry-content, .gallery, .wp-block-gallery';
    var GALLERY_CONTAINER_SELECTOR = '.gallery, .wp-block-gallery, .wpb_gallery, .ptg, .poetheme-gallery';
    var LIGHTBOX_CLASS = 'poetheme-lightbox';
    var ACTIVE_CLASS = 'is-active';
    var ZOOMED_CLASS = 'is-zoomed';
    var BODY_OPEN_CLASS = 'poetheme-lightbox-open';
    var TARGET_WIDTH = 1024;

    var lastActiveElement = null;
    var lightboxEl;
    var lightboxImage;
    var closeButton;
    var prevButton;
    var nextButton;
    var zoomButton;
    var counterEl;
    var lightboxContent;
    var galleryItems = [];
    var currentIndex = -1;
    var isZoomed = false;
    var touchStartX = 0;
    var touchStartY = 0;
    var touchInProgress = false;
    var lastTapTime = 0;
    var lastTapX = 0;
    var lastTapY = 0;
    var SWIPE_THRESHOLD = 40;
    var DOUBLE_TAP_DELAY = 320;
    var DOUBLE_TAP_RADIUS = 40;

    function setLightboxImage(url, altText) {
        if (!lightboxImage || !url) {
            return;
        }

        lightboxEl.classList.add('is-loading');
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

    function updateChrome() {
        var multiple = galleryItems.length > 1;

        if (prevButton) {
            prevButton.hidden = !multiple;
        }

        if (nextButton) {
            nextButton.hidden = !multiple;
        }

        if (counterEl) {
            counterEl.hidden = !multiple;

            if (multiple && currentIndex > -1) {
                counterEl.textContent = (currentIndex + 1) + ' / ' + galleryItems.length;
            }
        }
    }

    function preloadNeighbors() {
        if (galleryItems.length < 2) {
            return;
        }

        [currentIndex + 1, currentIndex - 1].forEach(function (neighbor) {
            if (neighbor < 0) {
                neighbor = galleryItems.length - 1;
            } else if (neighbor >= galleryItems.length) {
                neighbor = 0;
            }

            var item = galleryItems[neighbor];

            if (item && item.url) {
                var img = new Image();
                img.src = item.url;
            }
        });
    }

    function setZoom(enabled) {
        if (!lightboxEl) {
            return;
        }

        isZoomed = !!enabled;
        lightboxEl.classList.toggle(ZOOMED_CLASS, isZoomed);

        if (zoomButton) {
            zoomButton.setAttribute('aria-pressed', isZoomed ? 'true' : 'false');
            zoomButton.setAttribute('aria-label', isZoomed ? 'Riduci immagine' : 'Ingrandisci immagine');
        }

        if (!isZoomed && lightboxContent) {
            lightboxContent.scrollTop = 0;
            lightboxContent.scrollLeft = 0;
        } else if (isZoomed && lightboxContent) {
            // Centra il punto di partenza del pan sull'immagine ingrandita.
            window.requestAnimationFrame(function () {
                lightboxContent.scrollLeft = Math.max(0, (lightboxContent.scrollWidth - lightboxContent.clientWidth) / 2);
                lightboxContent.scrollTop = Math.max(0, (lightboxContent.scrollHeight - lightboxContent.clientHeight) / 2);
            });
        }
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

        setZoom(false);
        setLightboxImage(item.url, item.alt);
        updateChrome();
        preloadNeighbors();

        if (lightboxContent && document.activeElement !== lightboxContent && document.activeElement !== prevButton && document.activeElement !== nextButton) {
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
            touchInProgress = false;
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

        // Doppio tap: attiva/disattiva lo zoom.
        var now = Date.now();
        var isTap = Math.abs(deltaX) < 12 && Math.abs(deltaY) < 12;

        if (isTap) {
            var withinDelay = now - lastTapTime < DOUBLE_TAP_DELAY;
            var withinRadius = Math.abs(touch.clientX - lastTapX) < DOUBLE_TAP_RADIUS && Math.abs(touch.clientY - lastTapY) < DOUBLE_TAP_RADIUS;

            if (withinDelay && withinRadius) {
                lastTapTime = 0;
                setZoom(!isZoomed);
                return;
            }

            lastTapTime = now;
            lastTapX = touch.clientX;
            lastTapY = touch.clientY;
            return;
        }

        lastTapTime = 0;

        // Da ingrandita l'immagine si sposta con lo scroll: niente swipe.
        if (isZoomed) {
            return;
        }

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
                '<img class="poetheme-lightbox__image" alt="" loading="lazy" decoding="async" />' +
            '</div>' +
            '<button type="button" class="poetheme-lightbox__close" aria-label="Chiudi">&times;</button>' +
            '<button type="button" class="poetheme-lightbox__zoom" aria-label="Ingrandisci immagine" aria-pressed="false">' +
                '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="16.5" y1="16.5" x2="21" y2="21"></line><line x1="8" y1="11" x2="14" y2="11"></line><line x1="11" y1="8" x2="11" y2="14"></line></svg>' +
            '</button>' +
            '<button type="button" class="poetheme-lightbox__nav poetheme-lightbox__nav--prev" aria-label="Immagine precedente" hidden>&#10094;</button>' +
            '<button type="button" class="poetheme-lightbox__nav poetheme-lightbox__nav--next" aria-label="Immagine successiva" hidden>&#10095;</button>' +
            '<span class="poetheme-lightbox__counter" aria-live="polite" hidden></span>';

        document.body.appendChild(lightboxEl);

        lightboxImage = lightboxEl.querySelector('.poetheme-lightbox__image');
        closeButton = lightboxEl.querySelector('.poetheme-lightbox__close');
        zoomButton = lightboxEl.querySelector('.poetheme-lightbox__zoom');
        prevButton = lightboxEl.querySelector('.poetheme-lightbox__nav--prev');
        nextButton = lightboxEl.querySelector('.poetheme-lightbox__nav--next');
        counterEl = lightboxEl.querySelector('.poetheme-lightbox__counter');
        lightboxContent = lightboxEl.querySelector('.poetheme-lightbox__content');

        closeButton.addEventListener('click', closeLightbox);

        zoomButton.addEventListener('click', function () {
            setZoom(!isZoomed);
        });

        prevButton.addEventListener('click', function () {
            navigateLightbox(-1);
        });

        nextButton.addEventListener('click', function () {
            navigateLightbox(1);
        });

        lightboxEl.addEventListener('click', function (event) {
            if (event.target === lightboxEl || event.target === lightboxContent) {
                closeLightbox();
            }
        });

        if (lightboxImage) {
            lightboxImage.addEventListener('load', function () {
                lightboxEl.classList.remove('is-loading');
            });

            lightboxImage.addEventListener('error', function () {
                lightboxEl.classList.remove('is-loading');
            });

            lightboxImage.addEventListener('dblclick', function (event) {
                event.preventDefault();
                setZoom(!isZoomed);
            });
        }

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
                if (isZoomed) {
                    setZoom(false);
                } else {
                    closeLightbox();
                }
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
                return;
            }

            if (event.key === 'Home' && galleryItems.length > 1) {
                showImageAtIndex(0);
                event.preventDefault();
                return;
            }

            if (event.key === 'End' && galleryItems.length > 1) {
                showImageAtIndex(galleryItems.length - 1);
                event.preventDefault();
                return;
            }

            if (event.key === '+' || event.key === '=') {
                setZoom(true);
                event.preventDefault();
                return;
            }

            if (event.key === '-') {
                setZoom(false);
                event.preventDefault();
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

        // Il link alla versione a piena risoluzione ha priorità sul srcset:
        // è la destinazione esplicita scelta nel contenuto (es. gallerie .ptg).
        if (anchor && anchor.href && isImageLink(anchor.href)) {
            return anchor.href;
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
        var container = image ? image.closest(GALLERY_CONTAINER_SELECTOR) : null;
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

        setZoom(false);
        setLightboxImage(url, altText);
        updateChrome();
        preloadNeighbors();

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
        lightboxEl.classList.remove('is-loading');
        setZoom(false);
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
        lastTapTime = 0;
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
