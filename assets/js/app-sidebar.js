(function () {
    'use strict';

    var storageKey = 'poethemeAppSidebarCollapsed';
    var desktopQuery = '(min-width: 768px)';
    var lastDrawerTrigger = null;

    function getLabels() {
        return window.poethemeAppSidebar || {
            expandLabel: 'Espandi menu laterale',
            collapseLabel: 'Comprimi menu laterale',
            openMenuLabel: 'Apri menu mobile',
            closeMenuLabel: 'Chiudi menu mobile'
        };
    }

    function getStoredState() {
        try {
            return window.localStorage.getItem(storageKey) === '1';
        } catch (error) {
            return false;
        }
    }

    function setStoredState(isCollapsed) {
        try {
            window.localStorage.setItem(storageKey, isCollapsed ? '1' : '0');
        } catch (error) {}
    }

    function updateShell(shell, toggle, isCollapsed) {
        var labels = getLabels();
        var label = toggle ? toggle.querySelector('[data-poetheme-app-sidebar-toggle-label]') : null;

        shell.classList.toggle('is-sidebar-collapsed', isCollapsed);
        shell.classList.toggle('poetheme-app-shell--sidebar-expanded', !isCollapsed);

        if (toggle) {
            toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            toggle.setAttribute('aria-label', isCollapsed ? labels.expandLabel : labels.collapseLabel);
        }

        if (label) {
            label.textContent = isCollapsed ? labels.expandLabel : labels.collapseLabel;
        }
    }

    function initSidebarAccordion(scope) {
        var menus = scope.querySelectorAll('[data-poetheme-nav="1"][data-variant="sidebar"]');

        menus.forEach(function (menu) {
            var items = menu.querySelectorAll('li.menu-item-has-children');

            function setOpen(item, isOpen) {
                var submenu = item.querySelector(':scope > [data-poetheme-sidebar-submenu]');
                var toggles = item.querySelectorAll(':scope > .poetheme-sidebar-item-row > [data-poetheme-sidebar-submenu-toggle]');

                if (!submenu || !toggles.length) {
                    return;
                }

                item.classList.toggle('is-open', isOpen);
                submenu.hidden = !isOpen;
                submenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

                toggles.forEach(function (toggle) {
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            }

            items.forEach(function (item) {
                var submenu = item.querySelector(':scope > [data-poetheme-sidebar-submenu]');
                var toggles = item.querySelectorAll(':scope > .poetheme-sidebar-item-row > [data-poetheme-sidebar-submenu-toggle]');

                if (!submenu || !toggles.length) {
                    return;
                }

                setOpen(item, item.classList.contains('current-menu-ancestor') || item.classList.contains('current-menu-parent') || item.classList.contains('current-menu-item'));

                toggles.forEach(function (toggle) {
                    toggle.addEventListener('click', function (event) {
                        if (toggle.tagName && toggle.tagName.toLowerCase() === 'a') {
                            event.preventDefault();
                        }

                        setOpen(item, !item.classList.contains('is-open'));
                    });
                });

                item.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && item.classList.contains('is-open')) {
                        event.preventDefault();
                        setOpen(item, false);
                        toggles[0].focus();
                    }
                });
            });

            menu.setAttribute('data-ready', 'true');
        });
    }

    function getFocusable(container) {
        return Array.prototype.slice.call(
            container.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
        ).filter(function (element) {
            return element.offsetParent !== null || element === document.activeElement;
        });
    }

    function initMobileDrawer(shell) {
        var labels = getLabels();
        var toggle = document.querySelector('[data-poetheme-app-mobile-toggle]');
        var drawer = document.querySelector('[data-poetheme-app-mobile-drawer]');
        var overlay = document.querySelector('[data-poetheme-app-mobile-overlay]');
        var closeButton = document.querySelector('[data-poetheme-app-mobile-close]');
        var mediaQuery = window.matchMedia(desktopQuery);

        if (!toggle || !drawer || !overlay) {
            return;
        }

        function setDrawerOpen(isOpen) {
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-label', isOpen ? labels.closeMenuLabel : labels.openMenuLabel);
            drawer.classList.toggle('is-open', isOpen);
            overlay.classList.toggle('is-open', isOpen);
            drawer.hidden = !isOpen;
            overlay.hidden = !isOpen;
            drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.classList.toggle('poetheme-mobile-drawer-open', isOpen);

            if (shell) {
                shell.classList.toggle('is-mobile-drawer-open', isOpen);
            }

            if (isOpen) {
                lastDrawerTrigger = document.activeElement;
                window.setTimeout(function () {
                    var focusables = getFocusable(drawer);
                    (focusables[0] || drawer).focus();
                }, 0);
            } else if (lastDrawerTrigger && typeof lastDrawerTrigger.focus === 'function') {
                lastDrawerTrigger.focus();
                lastDrawerTrigger = null;
            }
        }

        function closeDrawer() {
            setDrawerOpen(false);
        }

        toggle.addEventListener('click', function () {
            setDrawerOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });

        if (closeButton) {
            closeButton.addEventListener('click', closeDrawer);
        }

        overlay.addEventListener('click', closeDrawer);

        drawer.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeDrawer();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            var focusables = getFocusable(drawer);
            var first = focusables[0];
            var last = focusables[focusables.length - 1];

            if (!first || !last) {
                return;
            }

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
                closeDrawer();
            }
        });

        function handleViewportChange(event) {
            if (event.matches && drawer.classList.contains('is-open')) {
                closeDrawer();
            }
        }

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleViewportChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleViewportChange);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var shell = document.querySelector('[data-poetheme-app-shell]');
        var toggle = document.querySelector('[data-poetheme-app-sidebar-toggle]');

        if (!shell) {
            return;
        }

        if (toggle) {
            updateShell(shell, toggle, getStoredState());

            toggle.addEventListener('click', function () {
                var isCollapsed = !shell.classList.contains('is-sidebar-collapsed');
                updateShell(shell, toggle, isCollapsed);
                setStoredState(isCollapsed);
            });
        }

        initSidebarAccordion(shell);
        initMobileDrawer(shell);
    });
}());
