(function () {
    'use strict';

    function initNavigation() {
        var menus = document.querySelectorAll('[data-poetheme-nav="1"]');
        if (!menus.length) {
            return;
        }

        menus.forEach(function (menu) {
            var variant = menu.getAttribute('data-variant') || 'desktop';
            if (variant === 'mobile') {
                setupMobileMenu(menu);
            } else {
                setupDesktopMenu(menu);
            }
        });
    }

    function setupDesktopMenu(menu) {
        var items = menu.querySelectorAll('li.menu-item-has-children');
        if (!items.length) {
            return;
        }

        items.forEach(function (item) {
            var trigger = item.querySelector(':scope > a[data-poetheme-toggle="submenu"]');
            var submenu = item.querySelector(':scope > [data-poetheme-submenu="true"]');
            if (!submenu) {
                return;
            }

            var closeTimer = null;

            var openMenu = function () {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                    closeTimer = null;
                }

                item.classList.add('is-open');
                submenu.classList.remove('hidden');
                submenu.classList.add('block');
                submenu.setAttribute('aria-hidden', 'false');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'true');
                }
            };

            var closeMenu = function () {
                item.classList.remove('is-open');
                submenu.classList.remove('block');
                submenu.classList.add('hidden');
                submenu.setAttribute('aria-hidden', 'true');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            };

            var scheduleClose = function () {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                }

                closeTimer = setTimeout(function () {
                    closeMenu();
                    closeTimer = null;
                }, 200);
            };

            var cancelClose = function () {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                    closeTimer = null;
                }
            };

            item.addEventListener('mouseenter', openMenu);
            item.addEventListener('mouseleave', scheduleClose);

            item.addEventListener('focusin', function () {
                cancelClose();
                openMenu();
            });

            item.addEventListener('focusout', function (event) {
                if (event.relatedTarget && item.contains(event.relatedTarget)) {
                    return;
                }
                scheduleClose();
            });

            item.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    event.stopPropagation();
                    closeMenu();
                    if (trigger) {
                        trigger.focus();
                    }
                }
            });

            submenu.addEventListener('mouseenter', cancelClose);
            submenu.addEventListener('mouseleave', scheduleClose);
        });
    }

    function setupMobileMenu(menu) {
        var items = menu.querySelectorAll('li.menu-item-has-children');
        if (!items.length) {
            return;
        }

        items.forEach(function (item) {
            var trigger = item.querySelector(':scope > a[data-poetheme-toggle="submenu"]');
            var submenu = item.querySelector(':scope > [data-poetheme-submenu="true"]');
            if (!trigger || !submenu) {
                return;
            }

            var openItem = function () {
                item.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                submenu.setAttribute('aria-hidden', 'false');
            };

            var closeItem = function () {
                item.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                submenu.setAttribute('aria-hidden', 'true');
            };

            if (item.classList.contains('current-menu-ancestor') || item.classList.contains('current-menu-item')) {
                openItem();
            } else {
                closeItem();
            }

            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                if (item.classList.contains('is-open')) {
                    closeItem();
                } else {
                    openItem();
                }
            });

            item.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeItem();
                    trigger.focus();
                }
            });
        });

        menu.setAttribute('data-ready', 'true');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavigation);
    } else {
        initNavigation();
    }
})();
