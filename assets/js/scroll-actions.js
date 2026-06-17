(function () {
    'use strict';

    function getLabels() {
        return window.poethemeScrollActions || {
            copyPrompt: 'Copia il link:',
            copied: 'Link copiato negli appunti.'
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-poetheme-scroll-actions]');

        if (!root) {
            return;
        }

        var toggle = root.querySelector('[data-poetheme-scroll-actions-toggle]');
        var topButton = root.querySelector('[data-poetheme-scroll-top]');
        var commentsLink = root.querySelector('[data-poetheme-scroll-comments]');
        var shareButton = root.querySelector('[data-poetheme-scroll-share]');
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var threshold = 300;

        root.hidden = false;

        function open() {
            root.classList.add('is-open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'true');
            }
        }

        function close() {
            root.classList.remove('is-open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }

        function onScroll() {
            if (window.pageYOffset > threshold) {
                root.classList.add('is-visible');
            } else {
                root.classList.remove('is-visible');
                close();
            }
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (root.classList.contains('is-open')) {
                    close();
                } else {
                    open();
                }
            });
        }

        if (topButton) {
            topButton.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
                close();
            });
        }

        if (commentsLink) {
            commentsLink.addEventListener('click', function (event) {
                var target = document.getElementById('comments');
                if (target) {
                    event.preventDefault();
                    target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
                }
                close();
            });
        }

        if (shareButton) {
            shareButton.addEventListener('click', function () {
                var url = window.location.href;
                var title = document.title;
                var labels = getLabels();

                if (navigator.share) {
                    navigator.share({ title: title, url: url }).catch(function () {});
                } else if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        window.alert(labels.copied);
                    }).catch(function () {
                        window.prompt(labels.copyPrompt, url);
                    });
                } else {
                    window.prompt(labels.copyPrompt, url);
                }

                close();
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
            }
        });

        document.addEventListener('click', function (event) {
            if (!root.contains(event.target)) {
                close();
            }
        });

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });
}());
