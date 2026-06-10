(function () {
    'use strict';

    var storageKey = 'poethemeAppSidebarCollapsed';

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
        var label = toggle ? toggle.querySelector('[data-poetheme-app-sidebar-toggle-label]') : null;

        shell.classList.toggle('is-sidebar-collapsed', isCollapsed);
        shell.classList.toggle('poetheme-app-shell--sidebar-expanded', !isCollapsed);

        if (toggle) {
            toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            toggle.setAttribute(
                'aria-label',
                isCollapsed ? poethemeAppSidebar.expandLabel : poethemeAppSidebar.collapseLabel
            );
        }

        if (label) {
            label.textContent = isCollapsed ? poethemeAppSidebar.expandLabel : poethemeAppSidebar.collapseLabel;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var shell = document.querySelector('[data-poetheme-app-shell]');
        var toggle = document.querySelector('[data-poetheme-app-sidebar-toggle]');

        if (!shell || !toggle || typeof poethemeAppSidebar === 'undefined') {
            return;
        }

        updateShell(shell, toggle, getStoredState());

        toggle.addEventListener('click', function () {
            var isCollapsed = !shell.classList.contains('is-sidebar-collapsed');
            updateShell(shell, toggle, isCollapsed);
            setStoredState(isCollapsed);
        });
    });
}());
