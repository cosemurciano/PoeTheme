(function ($) {
    'use strict';

    const config = window.poethemeMenuIcons || {};
    const groups = Array.isArray(config.groups) ? config.groups : [];
    let modalBackdrop;
    let modal;
    let searchInput;
    let gridContainer;
    let emptyState;
    let activeInput = null;
    let currentGroup = 'all';

    function createModal() {
        if (modalBackdrop) {
            return;
        }

        modalBackdrop = $('<div>', {
            class: 'poetheme-icon-modal-backdrop',
            'aria-hidden': 'true'
        });

        modal = $('<div>', {
            class: 'poetheme-icon-modal',
            role: 'dialog',
            'aria-modal': 'true'
        }).appendTo(modalBackdrop);

        const header = $('<div>', { class: 'poetheme-icon-modal__header' }).appendTo(modal);
        $('<h2>', { text: config.selectLabel || 'Seleziona icona' }).appendTo(header);
        $('<button>', {
            type: 'button',
            class: 'poetheme-icon-modal__close',
            html: '&times;'
        }).appendTo(header).on('click', closeModal);

        const filters = $('<div>', { class: 'poetheme-icon-modal__filters' }).appendTo(modal);
        const allButton = $('<button>', {
            type: 'button',
            text: config.allLabel || 'Tutte',
            class: 'is-active',
            'data-group': 'all'
        }).appendTo(filters);
        allButton.on('click', () => setGroup('all', allButton));

        groups.forEach((group, index) => {
            const button = $('<button>', {
                type: 'button',
                text: group.label || `Gruppo ${index + 1}`,
                'data-group': String(index)
            });
            button.on('click', () => setGroup(String(index), button));
            filters.append(button);
        });

        const searchWrapper = $('<div>', { class: 'poetheme-icon-modal__search' }).appendTo(modal);
        searchInput = $('<input>', {
            type: 'search',
            placeholder: config.searchLabel || 'Cerca icone...'
        }).appendTo(searchWrapper);
        searchInput.on('input', renderIcons);

        const body = $('<div>', { class: 'poetheme-icon-modal__body' }).appendTo(modal);
        gridContainer = $('<div>', { class: 'poetheme-icon-modal__grid' }).appendTo(body);
        emptyState = $('<div>', {
            class: 'poetheme-icon-modal__empty',
            text: config.noResults || 'Nessuna icona trovata.'
        }).appendTo(body);

        $('body').append(modalBackdrop);

        modalBackdrop.on('click', function (event) {
            if ($(event.target).is(modalBackdrop)) {
                closeModal();
            }
        });

        $(document).on('keydown.poethemeIconPicker', function (event) {
            if (event.key === 'Escape' && modalBackdrop.hasClass('is-visible')) {
                closeModal();
            }
        });
    }

    function setGroup(group, button) {
        currentGroup = group;
        button.closest('.poetheme-icon-modal__filters').find('button').removeClass('is-active');
        button.addClass('is-active');
        renderIcons();
    }

    function filterIcons() {
        const searchTerm = (searchInput ? searchInput.val() : '').trim().toLowerCase();
        let iconList = [];

        if ('all' === currentGroup) {
            groups.forEach((group) => {
                if (Array.isArray(group.icons)) {
                    iconList = iconList.concat(group.icons);
                }
            });
        } else {
            const index = parseInt(currentGroup, 10);
            if (!Number.isNaN(index) && groups[index] && Array.isArray(groups[index].icons)) {
                iconList = groups[index].icons.slice();
            }
        }

        if (searchTerm) {
            iconList = iconList.filter((icon) => icon.toLowerCase().includes(searchTerm));
        }

        iconList = Array.from(new Set(iconList));

        return iconList;
    }

    function renderIcons() {
        if (!gridContainer) {
            return;
        }

        gridContainer.empty();
        const icons = filterIcons();

        if (!icons.length) {
            emptyState.show();
            return;
        }

        emptyState.hide();

        icons.forEach((icon) => {
            const button = $('<button>', {
                type: 'button',
                class: 'poetheme-icon-option',
                'data-icon': icon
            });

            $('<i>', { 'data-lucide': icon }).appendTo(button);
            $('<span>', { text: icon }).appendTo(button);

            button.on('click', () => selectIcon(icon));
            gridContainer.append(button);
        });

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function openModal(input) {
        createModal();
        activeInput = $(input);
        if (searchInput) {
            searchInput.val('');
        }
        currentGroup = 'all';
        if (modal) {
            modal.find('.poetheme-icon-modal__filters button').removeClass('is-active').each(function (index) {
                if (0 === index) {
                    $(this).addClass('is-active');
                }
            });
        }

        modalBackdrop.addClass('is-visible').attr('aria-hidden', 'false');
        renderIcons();
        if (searchInput) {
            setTimeout(() => searchInput.trigger('focus'), 50);
        }
    }

    function closeModal() {
        if (!modalBackdrop) {
            return;
        }

        modalBackdrop.removeClass('is-visible').attr('aria-hidden', 'true');
        activeInput = null;
    }

    function updatePreview($input) {
        const preview = $input.closest('.poetheme-menu-icon-control').find('.poetheme-menu-icon-preview');
        const value = ($input.val() || '').trim();
        const emptyLabel = preview.data('empty-label') || '';

        if (!value) {
            preview.html('<span class="poetheme-menu-icon-placeholder">' + emptyLabel + '</span>');
        } else {
            const markup = '<span class="poetheme-menu-icon-example"><i data-lucide="' + value + '" class="w-4 h-4"></i></span>' +
                '<span class="poetheme-menu-icon-name">' + value + '</span>';
            preview.html(markup);
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function selectIcon(icon) {
        if (!activeInput) {
            return;
        }

        activeInput.val(icon).trigger('change');
        updatePreview(activeInput);
        closeModal();
    }

    function clearIcon(target) {
        const $input = $(target);
        $input.val('');
        updatePreview($input);
    }

    $(document).on('click', '.poetheme-open-icon-picker', function (event) {
        event.preventDefault();
        const target = $(this).data('target');
        if (!target) {
            return;
        }

        const $input = $(target);
        if ($input.length) {
            openModal($input);
        }
    });

    $(document).on('click', '.poetheme-clear-icon', function (event) {
        event.preventDefault();
        const target = $(this).data('target');
        if (!target) {
            return;
        }

        const $input = $(target);
        if ($input.length) {
            clearIcon($input);
        }
    });

    $(document).ready(function () {
        $('.poetheme-menu-icon-input').each(function () {
            updatePreview($(this));
        });

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
})(jQuery);
