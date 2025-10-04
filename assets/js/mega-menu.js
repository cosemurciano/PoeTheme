(function(){
    function megaMenuController() {
        return {
            isOpen: false,
            open() {
                this.isOpen = true;
            },
            close() {
                this.isOpen = false;
            }
        };
    }

    window.poethemeMegaMenu = megaMenuController;
})();
