(function(){
    function megaMenuController() {
        return {
            isOpen: false,
            closeTimeout: null,
            open() {
                if ( this.closeTimeout ) {
                    clearTimeout( this.closeTimeout );
                    this.closeTimeout = null;
                }

                this.isOpen = true;
            },
            scheduleClose() {
                if ( this.closeTimeout ) {
                    clearTimeout( this.closeTimeout );
                }

                this.closeTimeout = setTimeout( () => {
                    this.isOpen      = false;
                    this.closeTimeout = null;
                }, 200 );
            },
            close() {
                if ( this.closeTimeout ) {
                    clearTimeout( this.closeTimeout );
                    this.closeTimeout = null;
                }

                this.isOpen = false;
            },
            toggle() {
                if ( this.isOpen ) {
                    this.close();
                } else {
                    this.open();
                }
            }
        };
    }

    window.poethemeMegaMenu = megaMenuController;
})();
