/**
 * Media Sync admin screen: preview and batched sync of unregistered
 * uploads files into the Media Library.
 *
 * @package PoeTheme
 */
( function () {
    'use strict';

    if ( typeof window.poethemeMediaSync === 'undefined' ) {
        return;
    }

    var config   = window.poethemeMediaSync;
    var btnPrev  = document.getElementById( 'poetheme-media-sync-preview' );
    var btnRun   = document.getElementById( 'poetheme-media-sync-run' );
    var progress = document.getElementById( 'poetheme-media-sync-progress' );
    var bar      = document.getElementById( 'poetheme-media-sync-bar' );
    var status   = document.getElementById( 'poetheme-media-sync-status' );
    var log      = document.getElementById( 'poetheme-media-sync-log' );

    if ( ! btnPrev || ! btnRun ) {
        return;
    }

    var state = {
        running: false,
        total: 0,
        registered: 0,
        failed: [],
    };

    function sprintf( template ) {
        var args = Array.prototype.slice.call( arguments, 1 );
        var i    = 0;

        return template.replace( /%(\d+\$)?d/g, function ( match, position ) {
            if ( position ) {
                return args[ parseInt( position, 10 ) - 1 ];
            }
            return args[ i++ ];
        } );
    }

    function setBusy( busy ) {
        state.running   = busy;
        btnPrev.disabled = busy;
        btnRun.disabled  = busy;
    }

    function addLog( text, isError ) {
        var item = document.createElement( 'li' );
        item.textContent = text;
        if ( isError ) {
            item.style.color = '#b32d2e';
        }
        log.appendChild( item );
        log.scrollTop = log.scrollHeight;
    }

    function request( mode ) {
        var body = new FormData();
        body.append( 'action', 'poetheme_media_sync' );
        body.append( 'nonce', config.nonce );
        body.append( 'mode', mode );
        body.append( 'folder', document.getElementById( 'poetheme-media-sync-folder' ).value );
        body.append( 'date_from', document.getElementById( 'poetheme-media-sync-from' ).value );
        body.append( 'date_to', document.getElementById( 'poetheme-media-sync-to' ).value );

        if ( document.getElementById( 'poetheme-media-sync-thumbs' ).checked ) {
            body.append( 'thumbs', '1' );
        }

        if ( state.failed.length ) {
            body.append( 'exclude', JSON.stringify( state.failed.map( function ( entry ) {
                return entry.file;
            } ) ) );
        }

        return window.fetch( config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body,
        } ).then( function ( response ) {
            return response.json();
        } );
    }

    function preview() {
        log.innerHTML = '';
        progress.style.display = 'none';
        status.textContent = config.i18n.scanning;
        setBusy( true );

        request( 'preview' ).then( function ( payload ) {
            setBusy( false );

            if ( ! payload || ! payload.success ) {
                status.textContent = config.i18n.error;
                return;
            }

            var total = payload.data.total;

            if ( ! total ) {
                status.textContent = config.i18n.nothing;
                return;
            }

            status.textContent = sprintf( config.i18n.preview, total );
            payload.data.sample.forEach( function ( file ) {
                addLog( file );
            } );
        } ).catch( function () {
            setBusy( false );
            status.textContent = config.i18n.error;
        } );
    }

    function runBatch() {
        request( 'run' ).then( function ( payload ) {
            if ( ! payload || ! payload.success ) {
                setBusy( false );
                status.textContent = config.i18n.error;
                return;
            }

            var data = payload.data;

            if ( ! state.total ) {
                state.total = data.total;
            }

            state.registered += data.registered.length;
            state.failed = state.failed.concat( data.failed );

            data.registered.forEach( function ( file ) {
                addLog( file );
            } );
            data.failed.forEach( function ( entry ) {
                addLog( entry.file + ' — ' + entry.message, true );
            } );

            var doneCount = state.registered + state.failed.length;
            var percent   = state.total ? Math.min( 100, Math.round( ( doneCount / state.total ) * 100 ) ) : 100;
            bar.style.width = percent + '%';
            status.textContent = sprintf( config.i18n.running, doneCount, state.total );

            if ( data.done || ( ! data.registered.length && ! data.failed.length ) ) {
                setBusy( false );
                bar.style.width = '100%';
                status.textContent = sprintf( config.i18n.done, state.registered, state.failed.length );
                return;
            }

            runBatch();
        } ).catch( function () {
            setBusy( false );
            status.textContent = config.i18n.error;
        } );
    }

    btnPrev.addEventListener( 'click', preview );

    btnRun.addEventListener( 'click', function () {
        if ( state.running ) {
            return;
        }

        if ( ! window.confirm( config.i18n.confirmRun ) ) {
            return;
        }

        log.innerHTML = '';
        state.total = 0;
        state.registered = 0;
        state.failed = [];
        bar.style.width = '0';
        progress.style.display = 'block';
        status.textContent = config.i18n.scanning;
        setBusy( true );
        runBatch();
    } );
} )();
