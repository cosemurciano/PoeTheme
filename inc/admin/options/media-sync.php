<?php
/**
 * Media Sync: reindicizza i file presenti in wp-content/uploads nella
 * Libreria Media, senza duplicarli.
 *
 * Utile quando il database è stato perso o migrato ma i file media sono
 * ancora sul server: i file non registrati vengono aggiunti come attachment
 * (con metadati e, a scelta, miniature), rispettando i percorsi originali.
 *
 * Filtri disponibili: cartella di uploads (es. 2019/07) e intervallo di date
 * (data di modifica del file). Modalità anteprima (nessuna scrittura) e
 * sincronizzazione a lotti via AJAX per evitare timeout.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Estensioni escluse a prescindere dai mime consentiti.
 *
 * @return string[]
 */
function poetheme_media_sync_blocked_extensions() {
    return array( 'php', 'php5', 'phtml', 'html', 'htm', 'js', 'css', 'json', 'sql', 'ini', 'htaccess', 'log' );
}

/**
 * Register the Media Sync admin submenu.
 */
function poetheme_media_sync_admin_menu() {
    add_submenu_page(
        'poetheme-settings',
        __( 'Media Sync', 'poetheme' ),
        __( 'Media Sync', 'poetheme' ),
        'manage_options',
        'poetheme-media-sync',
        'poetheme_render_media_sync_page'
    );
}
add_action( 'admin_menu', 'poetheme_media_sync_admin_menu', 12 );

/**
 * Enqueue assets on the Media Sync screen.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_media_sync_admin_assets( $hook ) {
    if ( 'poetheme_page_poetheme-media-sync' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), poetheme_get_asset_version( 'assets/css/theme-options.css' ) );
    wp_enqueue_script( 'poetheme-media-sync', POETHEME_URI . '/assets/js/media-sync-admin.js', array(), poetheme_get_asset_version( 'assets/js/media-sync-admin.js' ), true );

    wp_localize_script(
        'poetheme-media-sync',
        'poethemeMediaSync',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'poetheme_media_sync' ),
            'i18n'    => array(
                'scanning'   => __( 'Scansione in corso…', 'poetheme' ),
                'preview'    => __( 'File da registrare: %d', 'poetheme' ),
                'nothing'    => __( 'Nessun file da registrare: la libreria è già sincronizzata per i filtri scelti.', 'poetheme' ),
                'running'    => __( 'Registrati %1$d di %2$d file…', 'poetheme' ),
                'done'       => __( 'Sincronizzazione completata: %1$d file registrati, %2$d errori.', 'poetheme' ),
                'error'      => __( 'Errore di comunicazione con il server. Riprova.', 'poetheme' ),
                'confirmRun' => __( 'Registrare i file trovati nella Libreria Media?', 'poetheme' ),
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'poetheme_media_sync_admin_assets' );

/**
 * List the subfolders of the uploads directory (max depth 2, e.g. "2019/07").
 *
 * @return string[] Relative folder paths.
 */
function poetheme_media_sync_get_upload_folders() {
    $uploads = wp_get_upload_dir();
    $basedir = $uploads['basedir'];
    $folders = array();

    if ( ! is_dir( $basedir ) ) {
        return $folders;
    }

    $level1 = glob( $basedir . '/*', GLOB_ONLYDIR );
    $level1 = is_array( $level1 ) ? $level1 : array();

    foreach ( $level1 as $dir1 ) {
        $rel1      = basename( $dir1 );
        $folders[] = $rel1;

        $level2 = glob( $dir1 . '/*', GLOB_ONLYDIR );
        $level2 = is_array( $level2 ) ? $level2 : array();

        foreach ( $level2 as $dir2 ) {
            $folders[] = $rel1 . '/' . basename( $dir2 );
        }
    }

    sort( $folders );

    return $folders;
}

/**
 * Sanitize and validate a relative uploads folder.
 *
 * @param string $folder Raw folder value.
 * @return string Validated relative folder ('' = whole uploads dir).
 */
function poetheme_media_sync_sanitize_folder( $folder ) {
    $folder = trim( (string) $folder, "/ \t\n\r" );

    if ( '' === $folder ) {
        return '';
    }

    if ( ! preg_match( '#^[A-Za-z0-9._\- /]+$#', $folder ) || false !== strpos( $folder, '..' ) ) {
        return '';
    }

    $uploads = wp_get_upload_dir();
    $real    = realpath( $uploads['basedir'] . '/' . $folder );
    $base    = realpath( $uploads['basedir'] );

    if ( ! $real || ! $base || 0 !== strpos( $real, $base ) ) {
        return '';
    }

    return $folder;
}

/**
 * Set of files already registered in the Media Library.
 *
 * @return array<string,bool> Keys are relative paths (as in _wp_attached_file).
 */
function poetheme_media_sync_registered_files() {
    global $wpdb;

    $values = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- lookup set, no user input.
    $set    = array();

    foreach ( (array) $values as $value ) {
        if ( $value ) {
            $set[ $value ] = true;
        }
    }

    return $set;
}

/**
 * Collect uploads files not yet registered in the Media Library.
 *
 * @param string $folder    Relative folder filter ('' = all).
 * @param int    $date_from Min file modification timestamp (0 = no limit).
 * @param int    $date_to   Max file modification timestamp (0 = no limit).
 * @param array  $exclude   Relative paths to skip (previous failures).
 * @return string[] Sorted relative paths.
 */
function poetheme_media_sync_collect( $folder = '', $date_from = 0, $date_to = 0, $exclude = array() ) {
    $uploads = wp_get_upload_dir();
    $basedir = rtrim( $uploads['basedir'], '/' );
    $target  = $folder ? $basedir . '/' . $folder : $basedir;

    if ( ! is_dir( $target ) ) {
        return array();
    }

    $registered  = poetheme_media_sync_registered_files();
    $exclude_set = array();
    foreach ( (array) $exclude as $item ) {
        $exclude_set[ (string) $item ] = true;
    }

    $blocked    = poetheme_media_sync_blocked_extensions();
    $candidates = array();

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $target, FilesystemIterator::SKIP_DOTS ),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ( $iterator as $file ) {
        if ( ! $file->isFile() ) {
            continue;
        }

        $name = $file->getFilename();

        if ( '.' === $name[0] ) {
            continue;
        }

        $ext = strtolower( $file->getExtension() );

        if ( '' === $ext || in_array( $ext, $blocked, true ) ) {
            continue;
        }

        $filetype = wp_check_filetype( $name );

        if ( empty( $filetype['type'] ) ) {
            continue;
        }

        // Miniature generate da WordPress (es. foto-300x225.jpg): appartengono
        // a un originale, non vanno registrate come attachment autonomi.
        if ( preg_match( '/-\d+x\d+\.[A-Za-z0-9]+$/', $name ) ) {
            continue;
        }

        $abs = $file->getPathname();
        $rel = ltrim( str_replace( '\\', '/', substr( $abs, strlen( $basedir ) ) ), '/' );

        // Copia "-scaled" di un originale già registrato: è già rappresentata
        // dall'attachment dell'originale, non va registrata di nuovo.
        if ( preg_match( '/-scaled\.[A-Za-z0-9]+$/', $name ) ) {
            $original_rel = preg_replace( '/-scaled(\.[A-Za-z0-9]+)$/', '$1', $rel );

            if ( isset( $registered[ $original_rel ] ) ) {
                continue;
            }
        }

        if ( isset( $registered[ $rel ] ) || isset( $exclude_set[ $rel ] ) ) {
            continue;
        }

        $mtime = $file->getMTime();

        if ( $date_from && $mtime < $date_from ) {
            continue;
        }

        if ( $date_to && $mtime > $date_to ) {
            continue;
        }

        $candidates[] = $rel;
    }

    sort( $candidates );

    return $candidates;
}

/**
 * Register a single uploads file as an attachment.
 *
 * @param string $rel             Relative path inside uploads.
 * @param bool   $generate_thumbs Whether to generate missing intermediate sizes.
 * @return int|WP_Error Attachment ID or error.
 */
function poetheme_media_sync_register_file( $rel, $generate_thumbs = true ) {
    $uploads = wp_get_upload_dir();
    $abs     = rtrim( $uploads['basedir'], '/' ) . '/' . $rel;

    if ( ! file_exists( $abs ) ) {
        return new WP_Error( 'missing', __( 'File non trovato.', 'poetheme' ) );
    }

    $filetype = wp_check_filetype( basename( $abs ) );

    if ( empty( $filetype['type'] ) ) {
        return new WP_Error( 'type', __( 'Tipo di file non consentito.', 'poetheme' ) );
    }

    // Data del post: dalla cartella anno/mese se presente, altrimenti dalla
    // data di modifica del file (così l'attachment resta nella sua cartella).
    if ( preg_match( '#^(\d{4})/(\d{2})/#', $rel, $m ) ) {
        $post_date = sprintf( '%s-%s-01 12:00:00', $m[1], $m[2] );
    } else {
        $post_date = gmdate( 'Y-m-d H:i:s', filemtime( $abs ) );
    }

    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_text_field( pathinfo( basename( $abs ), PATHINFO_FILENAME ) ),
        'post_status'    => 'inherit',
        'post_date'      => $post_date,
        'post_date_gmt'  => $post_date,
    );

    $attach_id = wp_insert_attachment( $attachment, $abs );

    if ( is_wp_error( $attach_id ) ) {
        return $attach_id;
    }

    if ( ! $attach_id ) {
        return new WP_Error( 'insert', __( 'Registrazione non riuscita.', 'poetheme' ) );
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $disable_sizes = null;

    if ( ! $generate_thumbs ) {
        $disable_sizes = function () {
            return array();
        };
        add_filter( 'intermediate_image_sizes_advanced', $disable_sizes );
    }

    $metadata = wp_generate_attachment_metadata( $attach_id, $abs );

    if ( $disable_sizes ) {
        remove_filter( 'intermediate_image_sizes_advanced', $disable_sizes );
    }

    if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
        wp_update_attachment_metadata( $attach_id, $metadata );
    }

    return $attach_id;
}

/**
 * AJAX endpoint: preview or run a sync batch.
 */
function poetheme_media_sync_ajax() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_send_json_error( array( 'message' => __( 'Permesso negato.', 'poetheme' ) ), 403 );
    }

    check_ajax_referer( 'poetheme_media_sync', 'nonce' );

    $mode      = isset( $_POST['mode'] ) && 'run' === $_POST['mode'] ? 'run' : 'preview';
    $folder    = isset( $_POST['folder'] ) ? poetheme_media_sync_sanitize_folder( wp_unslash( $_POST['folder'] ) ) : '';
    $thumbs    = ! empty( $_POST['thumbs'] );
    $date_from = 0;
    $date_to   = 0;

    if ( ! empty( $_POST['date_from'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', wp_unslash( $_POST['date_from'] ) ) ) {
        $date_from = strtotime( wp_unslash( $_POST['date_from'] ) . ' 00:00:00' );
    }

    if ( ! empty( $_POST['date_to'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', wp_unslash( $_POST['date_to'] ) ) ) {
        $date_to = strtotime( wp_unslash( $_POST['date_to'] ) . ' 23:59:59' );
    }

    $exclude = array();

    if ( ! empty( $_POST['exclude'] ) ) {
        $decoded = json_decode( wp_unslash( $_POST['exclude'] ), true );

        if ( is_array( $decoded ) ) {
            $exclude = array_slice( array_map( 'sanitize_text_field', $decoded ), 0, 500 );
        }
    }

    $candidates = poetheme_media_sync_collect( $folder, $date_from, $date_to, $exclude );
    $total      = count( $candidates );

    if ( 'preview' === $mode ) {
        wp_send_json_success(
            array(
                'total'  => $total,
                'sample' => array_slice( $candidates, 0, 50 ),
            )
        );
    }

    $batch_size = 20;
    $batch      = array_slice( $candidates, 0, $batch_size );
    $registered = array();
    $failed     = array();

    foreach ( $batch as $rel ) {
        $result = poetheme_media_sync_register_file( $rel, $thumbs );

        if ( is_wp_error( $result ) ) {
            $failed[] = array(
                'file'    => $rel,
                'message' => $result->get_error_message(),
            );
        } else {
            $registered[] = $rel;
        }
    }

    wp_send_json_success(
        array(
            'total'      => $total,
            'registered' => $registered,
            'failed'     => $failed,
            'remaining'  => max( 0, $total - count( $batch ) ),
            'done'       => count( $batch ) >= $total,
        )
    );
}
add_action( 'wp_ajax_poetheme_media_sync', 'poetheme_media_sync_ajax' );

/**
 * Render the Media Sync admin page.
 */
function poetheme_render_media_sync_page() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    $folders = poetheme_media_sync_get_upload_folders();
    $uploads = wp_get_upload_dir();
    ?>
    <div class="wrap poetheme-options">
        <h1><?php esc_html_e( 'Media Sync', 'poetheme' ); ?></h1>

        <div class="poetheme-panel">
            <div class="poetheme-panel__header">
                <h2><?php esc_html_e( 'Reindicizza i file di uploads nella Libreria Media', 'poetheme' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Registra nella Libreria Media i file già presenti sul server (ad esempio dopo la perdita o la migrazione del database), senza spostarli né duplicarli. Le miniature generate da WordPress (es. -300x225) vengono riconosciute e ignorate.', 'poetheme' ); ?>
                    <br>
                    <code><?php echo esc_html( $uploads['basedir'] ); ?></code>
                </p>
            </div>
            <div class="poetheme-panel__body">
                <table class="form-table poetheme-fields" role="presentation">
                    <tbody>
                        <tr class="poetheme-field">
                            <th scope="row" class="poetheme-field__label"><label for="poetheme-media-sync-folder"><?php esc_html_e( 'Cartella', 'poetheme' ); ?></label></th>
                            <td class="poetheme-field__control">
                                <select id="poetheme-media-sync-folder">
                                    <option value=""><?php esc_html_e( 'Tutte le cartelle di uploads', 'poetheme' ); ?></option>
                                    <?php foreach ( $folders as $folder ) : ?>
                                        <option value="<?php echo esc_attr( $folder ); ?>"><?php echo esc_html( $folder ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description poetheme-field__help"><?php esc_html_e( 'Limita la sincronizzazione a una cartella (es. 2019/07) oppure elabora tutta la libreria.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                        <tr class="poetheme-field">
                            <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Filtro per data', 'poetheme' ); ?></th>
                            <td class="poetheme-field__control">
                                <label for="poetheme-media-sync-from"><?php esc_html_e( 'Dal', 'poetheme' ); ?></label>
                                <input type="date" id="poetheme-media-sync-from">
                                <label for="poetheme-media-sync-to"><?php esc_html_e( 'al', 'poetheme' ); ?></label>
                                <input type="date" id="poetheme-media-sync-to">
                                <p class="description poetheme-field__help"><?php esc_html_e( 'Considera solo i file modificati nell’intervallo indicato (facoltativo).', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                        <tr class="poetheme-field">
                            <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Miniature', 'poetheme' ); ?></th>
                            <td class="poetheme-field__control">
                                <label for="poetheme-media-sync-thumbs">
                                    <input type="checkbox" id="poetheme-media-sync-thumbs" checked>
                                    <?php esc_html_e( 'Genera le miniature mancanti durante la registrazione', 'poetheme' ); ?>
                                </label>
                                <p class="description poetheme-field__help"><?php esc_html_e( 'Se le miniature esistono già sul server, WordPress le riutilizza. Disattiva per una sincronizzazione più veloce senza creare nuovi file.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="button" id="poetheme-media-sync-preview"><?php esc_html_e( 'Anteprima', 'poetheme' ); ?></button>
                    <button type="button" class="button button-primary" id="poetheme-media-sync-run"><?php esc_html_e( 'Sincronizza', 'poetheme' ); ?></button>
                </p>

                <div id="poetheme-media-sync-progress" style="display:none;max-width:640px;background:#dcdcde;border-radius:4px;overflow:hidden;height:16px;margin:12px 0;">
                    <div id="poetheme-media-sync-bar" style="width:0;height:100%;background:#2271b1;transition:width .3s ease;"></div>
                </div>

                <p id="poetheme-media-sync-status" role="status" aria-live="polite"></p>
                <ul id="poetheme-media-sync-log" style="max-height:280px;overflow:auto;font-family:monospace;font-size:12px;margin:0;"></ul>
            </div>
        </div>
    </div>
    <?php
}
