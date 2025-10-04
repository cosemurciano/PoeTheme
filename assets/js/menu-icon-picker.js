(function(){
    function updatePreview(inputId, icon) {
        var preview = document.querySelector('.poetheme-icon-picker__preview[data-target="' + inputId + '"]');
        if (!preview) {
            return;
        }

        preview.innerHTML = icon ? '<i data-lucide="' + icon + '" class="w-5 h-5"></i>' : '';
        if (window.lucide) {
            window.lucide.createIcons({root: preview});
        }
    }

    function handleIconSelect(event) {
        var target = event.target.closest('.poetheme-icon-picker__option');
        if (!target) {
            return;
        }

        event.preventDefault();

        var icon  = target.getAttribute('data-icon');
        var inputId = target.getAttribute('data-target');
        var input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        input.value = icon;
        updatePreview(inputId, icon);
    }

    function initPicker(container) {
        if (!container) {
            return;
        }

        container.addEventListener('click', handleIconSelect);

        var input = container.querySelector('.poetheme-icon-picker__input');
        if (input) {
            input.addEventListener('input', function(){
                updatePreview(input.id, input.value.trim());
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.poetheme-icon-picker').forEach(function(picker){
            initPicker(picker);
        });
    });
})();
