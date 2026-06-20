<?php
/**
 * Style palettes ("Palette cromatica e stile").
 *
 * Importable JSON presets that override colors, fonts and sizes for every
 * theme element. A palette is applied as a non-destructive, front-end-only
 * override on top of the saved options, so it works with any selected header
 * layout and can be deactivated to restore the manual settings.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Maximum accepted size for an imported palette file (bytes).
 */
if ( ! defined( 'POETHEME_PALETTE_MAX_BYTES' ) ) {
    define( 'POETHEME_PALETTE_MAX_BYTES', 262144 );
}

/**
 * Global option keys a palette is allowed to override.
 *
 * @return string[]
 */
function poetheme_get_palette_global_keys() {
    return array( 'layout_mode', 'site_width' );
}

/**
 * Retrieve all stored style palettes.
 *
 * @return array
 */
function poetheme_get_style_palettes() {
    $palettes = get_option( 'poetheme_style_palettes', array() );

    return is_array( $palettes ) ? $palettes : array();
}

/**
 * Retrieve the active palette id.
 *
 * @return string
 */
function poetheme_get_active_palette_id() {
    return (string) get_option( 'poetheme_active_palette', '' );
}

/**
 * Retrieve the active palette data, if any.
 *
 * @return array|null
 */
function poetheme_get_active_palette() {
    $id = poetheme_get_active_palette_id();

    if ( '' === $id ) {
        return null;
    }

    $palettes = poetheme_get_style_palettes();

    return isset( $palettes[ $id ] ) ? $palettes[ $id ] : null;
}

/**
 * Sanitize the global subset of a palette.
 *
 * @param array $input Raw global values.
 * @return array
 */
function poetheme_sanitize_palette_global( $input ) {
    $defaults = poetheme_get_default_global_options();
    $input    = is_array( $input ) ? $input : array();
    $output   = array();

    $layout            = isset( $input['layout_mode'] ) ? sanitize_key( $input['layout_mode'] ) : $defaults['layout_mode'];
    $output['layout_mode'] = in_array( $layout, array( 'full', 'boxed' ), true ) ? $layout : $defaults['layout_mode'];

    $width                 = isset( $input['site_width'] ) ? absint( $input['site_width'] ) : $defaults['site_width'];
    $output['site_width']  = max( 960, min( 1920, $width ) );

    return $output;
}

/**
 * Sanitize a decoded palette payload using the existing option sanitizers.
 *
 * @param array $data Decoded JSON payload.
 * @return array
 */
function poetheme_sanitize_palette_data( $data ) {
    $data = is_array( $data ) ? $data : array();

    $name = isset( $data['name'] ) ? sanitize_text_field( wp_strip_all_tags( (string) $data['name'] ) ) : '';
    if ( '' === $name ) {
        $name = __( 'Palette senza nome', 'poetheme' );
    }

    $colors = isset( $data['colors'] ) && is_array( $data['colors'] ) ? $data['colors'] : array();
    $fonts  = isset( $data['fonts'] ) && is_array( $data['fonts'] ) ? $data['fonts'] : array();
    $global = isset( $data['global'] ) && is_array( $data['global'] ) ? $data['global'] : array();

    return array(
        'name'   => $name,
        'colors' => poetheme_sanitize_color_options( $colors ),
        'fonts'  => poetheme_sanitize_font_options( $fonts ),
        'global' => poetheme_sanitize_palette_global( $global ),
    );
}

/**
 * Build an example palette payload from the theme defaults (download template).
 *
 * @return array
 */
function poetheme_build_example_palette() {
    return array(
        'name'   => __( 'Palette di esempio', 'poetheme' ),
        'colors' => poetheme_get_default_color_options(),
        'fonts'  => poetheme_get_default_font_options(),
        'global' => array(
            'layout_mode' => 'full',
            'site_width'  => 1200,
        ),
    );
}

/* -------------------------------------------------------------------------
 * Front-end override (non-destructive, applied on top of saved options).
 * ---------------------------------------------------------------------- */

/**
 * Override color options with the active palette on the front end.
 *
 * @param array $options Resolved color options.
 * @return array
 */
function poetheme_palette_apply_colors( $options ) {
    if ( is_admin() ) {
        return $options;
    }

    $palette = poetheme_get_active_palette();

    if ( $palette && ! empty( $palette['colors'] ) && is_array( $palette['colors'] ) ) {
        $options = array_merge( $options, $palette['colors'] );
    }

    return $options;
}
add_filter( 'poetheme_color_options', 'poetheme_palette_apply_colors' );

/**
 * Override font options with the active palette on the front end.
 *
 * @param array $options Resolved font options.
 * @return array
 */
function poetheme_palette_apply_fonts( $options ) {
    if ( is_admin() ) {
        return $options;
    }

    $palette = poetheme_get_active_palette();

    if ( $palette && ! empty( $palette['fonts'] ) && is_array( $palette['fonts'] ) ) {
        $options = array_merge( $options, $palette['fonts'] );
    }

    return $options;
}
add_filter( 'poetheme_font_options', 'poetheme_palette_apply_fonts' );

/**
 * Override the global option subset with the active palette on the front end.
 *
 * @param array $options Resolved global options.
 * @return array
 */
function poetheme_palette_apply_global( $options ) {
    if ( is_admin() ) {
        return $options;
    }

    $palette = poetheme_get_active_palette();

    if ( $palette && ! empty( $palette['global'] ) && is_array( $palette['global'] ) ) {
        foreach ( poetheme_get_palette_global_keys() as $key ) {
            if ( isset( $palette['global'][ $key ] ) ) {
                $options[ $key ] = $palette['global'][ $key ];
            }
        }
    }

    return $options;
}
add_filter( 'poetheme_global_options', 'poetheme_palette_apply_global' );

/**
 * Ensure the font-settings body class is present when a palette is active so
 * the palette font sizes/rules apply even without a custom font family.
 *
 * @param array $classes Body classes.
 * @return array
 */
function poetheme_palette_body_class( $classes ) {
    if ( ! is_admin() && poetheme_get_active_palette() ) {
        if ( ! in_array( 'poetheme-has-font-settings', $classes, true ) ) {
            $classes[] = 'poetheme-has-font-settings';
        }
    }

    return $classes;
}
add_filter( 'body_class', 'poetheme_palette_body_class', 20 );

/* -------------------------------------------------------------------------
 * Admin page + handlers.
 * ---------------------------------------------------------------------- */

/**
 * Register the palette admin submenu.
 */
function poetheme_palette_admin_menu() {
    add_submenu_page(
        'poetheme-settings',
        __( 'Palette cromatica e stile', 'poetheme' ),
        __( 'Palette e stile', 'poetheme' ),
        'manage_options',
        'poetheme-palette',
        'poetheme_render_palette_page'
    );
}
add_action( 'admin_menu', 'poetheme_palette_admin_menu', 11 );

/**
 * Enqueue the options stylesheet on the palette screen.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_palette_admin_assets( $hook ) {
    if ( 'poetheme_page_poetheme-palette' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), poetheme_get_asset_version( 'assets/css/theme-options.css' ) );
}
add_action( 'admin_enqueue_scripts', 'poetheme_palette_admin_assets' );

/**
 * Redirect back to the palette page with a notice code.
 *
 * @param string $notice Notice code.
 */
function poetheme_palette_redirect( $notice ) {
    $url = add_query_arg( 'poetheme_palette_notice', $notice, admin_url( 'admin.php?page=poetheme-palette' ) );
    wp_safe_redirect( $url );
    exit;
}

/**
 * Handle a palette JSON import.
 */
function poetheme_handle_palette_import() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_import' );

    if ( empty( $_FILES['palette_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['palette_file']['tmp_name'] ) ) {
        poetheme_palette_redirect( 'import_error' );
    }

    if ( ! empty( $_FILES['palette_file']['error'] ) ) {
        poetheme_palette_redirect( 'import_error' );
    }

    $size = isset( $_FILES['palette_file']['size'] ) ? (int) $_FILES['palette_file']['size'] : 0;
    if ( $size <= 0 || $size > POETHEME_PALETTE_MAX_BYTES ) {
        poetheme_palette_redirect( 'import_size' );
    }

    $contents = file_get_contents( $_FILES['palette_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a freshly uploaded temp file.
    $data     = json_decode( (string) $contents, true );

    if ( ! is_array( $data ) ) {
        poetheme_palette_redirect( 'import_invalid' );
    }

    $palette            = poetheme_sanitize_palette_data( $data );
    $palette['created'] = time();

    $palettes = poetheme_get_style_palettes();

    $id = 'pal_' . wp_generate_password( 10, false, false );
    while ( isset( $palettes[ $id ] ) ) {
        $id = 'pal_' . wp_generate_password( 10, false, false );
    }

    $palettes[ $id ] = $palette;
    update_option( 'poetheme_style_palettes', $palettes );

    poetheme_palette_redirect( 'imported' );
}
add_action( 'admin_post_poetheme_palette_import', 'poetheme_handle_palette_import' );

/**
 * Activate a stored palette.
 */
function poetheme_handle_palette_activate() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_activate' );

    $id       = isset( $_POST['palette'] ) ? sanitize_text_field( wp_unslash( $_POST['palette'] ) ) : '';
    $palettes = poetheme_get_style_palettes();

    if ( isset( $palettes[ $id ] ) ) {
        update_option( 'poetheme_active_palette', $id );
        poetheme_palette_redirect( 'activated' );
    }

    poetheme_palette_redirect( 'error' );
}
add_action( 'admin_post_poetheme_palette_activate', 'poetheme_handle_palette_activate' );

/**
 * Deactivate the active palette.
 */
function poetheme_handle_palette_deactivate() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_deactivate' );

    update_option( 'poetheme_active_palette', '' );
    poetheme_palette_redirect( 'deactivated' );
}
add_action( 'admin_post_poetheme_palette_deactivate', 'poetheme_handle_palette_deactivate' );

/**
 * Delete a stored palette.
 */
function poetheme_handle_palette_delete() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_delete' );

    $id       = isset( $_POST['palette'] ) ? sanitize_text_field( wp_unslash( $_POST['palette'] ) ) : '';
    $palettes = poetheme_get_style_palettes();

    if ( isset( $palettes[ $id ] ) ) {
        unset( $palettes[ $id ] );
        update_option( 'poetheme_style_palettes', $palettes );

        if ( poetheme_get_active_palette_id() === $id ) {
            update_option( 'poetheme_active_palette', '' );
        }
    }

    poetheme_palette_redirect( 'deleted' );
}
add_action( 'admin_post_poetheme_palette_delete', 'poetheme_handle_palette_delete' );

/**
 * Stream a palette payload as a downloadable JSON file.
 *
 * @param array  $payload  Data to encode.
 * @param string $filename Suggested file name.
 */
function poetheme_palette_stream_json( $payload, $filename ) {
    nocache_headers();
    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    exit;
}

/**
 * Download the example palette template.
 */
function poetheme_handle_palette_example() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_example' );

    poetheme_palette_stream_json( poetheme_build_example_palette(), 'poetheme-palette-esempio.json' );
}
add_action( 'admin_post_poetheme_palette_example', 'poetheme_handle_palette_example' );

/**
 * Export a stored palette as JSON.
 */
function poetheme_handle_palette_export() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_palette_export' );

    $id       = isset( $_GET['palette'] ) ? sanitize_text_field( wp_unslash( $_GET['palette'] ) ) : '';
    $palettes = poetheme_get_style_palettes();

    if ( ! isset( $palettes[ $id ] ) ) {
        wp_die( esc_html__( 'Palette non trovata.', 'poetheme' ) );
    }

    $palette  = $palettes[ $id ];
    $payload  = array(
        'name'   => isset( $palette['name'] ) ? $palette['name'] : '',
        'colors' => isset( $palette['colors'] ) ? $palette['colors'] : array(),
        'fonts'  => isset( $palette['fonts'] ) ? $palette['fonts'] : array(),
        'global' => isset( $palette['global'] ) ? $palette['global'] : array(),
    );
    $slug     = sanitize_title( $payload['name'] );
    $filename = 'poetheme-palette-' . ( $slug ? $slug : $id ) . '.json';

    poetheme_palette_stream_json( $payload, $filename );
}
add_action( 'admin_post_poetheme_palette_export', 'poetheme_handle_palette_export' );

/**
 * Map a notice code to a human message.
 *
 * @param string $code Notice code.
 * @return array{type:string,message:string}|null
 */
function poetheme_get_palette_notice( $code ) {
    $notices = array(
        'imported'       => array( 'success', __( 'Palette importata con successo.', 'poetheme' ) ),
        'activated'      => array( 'success', __( 'Palette applicata.', 'poetheme' ) ),
        'deactivated'    => array( 'success', __( 'Palette disattivata. Sono ripristinate le impostazioni manuali.', 'poetheme' ) ),
        'deleted'        => array( 'success', __( 'Palette eliminata.', 'poetheme' ) ),
        'import_error'   => array( 'error', __( 'Caricamento del file non riuscito.', 'poetheme' ) ),
        'import_size'    => array( 'error', __( 'Il file supera la dimensione massima consentita.', 'poetheme' ) ),
        'import_invalid' => array( 'error', __( 'Il file JSON non è valido.', 'poetheme' ) ),
        'error'          => array( 'error', __( 'Operazione non riuscita.', 'poetheme' ) ),
    );

    if ( ! isset( $notices[ $code ] ) ) {
        return null;
    }

    return array(
        'type'    => $notices[ $code ][0],
        'message' => $notices[ $code ][1],
    );
}

/**
 * Render a small set of color swatches for a palette preview.
 *
 * @param array $colors Palette colors.
 */
function poetheme_render_palette_swatches( $colors ) {
    $swatches = array(
        'page_background_color'   => __( 'Sfondo pagina', 'poetheme' ),
        'content_background_color'=> __( 'Sfondo contenuto', 'poetheme' ),
        'content_text_color'      => __( 'Testo', 'poetheme' ),
        'content_link_color'      => __( 'Link', 'poetheme' ),
        'header_background_color'  => __( 'Sfondo testata', 'poetheme' ),
        'menu_active_link_color'   => __( 'Menu attivo', 'poetheme' ),
        'cta_background_color'     => __( 'Pulsante CTA', 'poetheme' ),
    );

    echo '<div class="poetheme-palette-swatches">';
    foreach ( $swatches as $key => $label ) {
        $value = isset( $colors[ $key ] ) && '' !== $colors[ $key ] ? $colors[ $key ] : 'transparent';
        printf(
            '<span class="poetheme-palette-swatch" title="%1$s" style="background:%2$s"><span class="screen-reader-text">%1$s</span></span>',
            esc_attr( $label . ': ' . $value ),
            esc_attr( $value )
        );
    }
    echo '</div>';
}

/**
 * Render the "Palette cromatica e stile" admin page.
 */
function poetheme_render_palette_page() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    $palettes  = poetheme_get_style_palettes();
    $active_id = poetheme_get_active_palette_id();
    $post_url  = esc_url( admin_url( 'admin-post.php' ) );

    $example_url = wp_nonce_url( admin_url( 'admin-post.php?action=poetheme_palette_example' ), 'poetheme_palette_example' );

    $notice_code = isset( $_GET['poetheme_palette_notice'] ) ? sanitize_key( wp_unslash( $_GET['poetheme_palette_notice'] ) ) : '';
    $notice      = $notice_code ? poetheme_get_palette_notice( $notice_code ) : null;
    ?>
    <div class="wrap poetheme-options poetheme-palette-page">
        <h1><?php esc_html_e( 'Palette cromatica e stile', 'poetheme' ); ?></h1>

        <?php if ( $notice ) : ?>
            <div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
                <p><?php echo esc_html( $notice['message'] ); ?></p>
            </div>
        <?php endif; ?>

        <p class="description">
            <?php esc_html_e( 'Importa un file JSON per creare una palette che assegna colori, font e dimensioni a tutti gli elementi del tema. La palette applicata sovrascrive qualsiasi impostazione e si applica a qualsiasi testata selezionata, senza modificarne la struttura. Puoi disattivarla per ripristinare le impostazioni manuali.', 'poetheme' ); ?>
        </p>

        <div class="poetheme-palette-toolbar">
            <a class="button" href="<?php echo esc_url( $example_url ); ?>">
                <?php esc_html_e( 'Scarica JSON di esempio', 'poetheme' ); ?>
            </a>

            <form class="poetheme-palette-import" action="<?php echo $post_url; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="poetheme_palette_import" />
                <?php wp_nonce_field( 'poetheme_palette_import' ); ?>
                <input type="file" name="palette_file" accept="application/json,.json" required />
                <?php submit_button( __( 'Importa palette', 'poetheme' ), 'primary', 'submit', false ); ?>
            </form>
        </div>

        <?php if ( '' !== $active_id && isset( $palettes[ $active_id ] ) ) : ?>
            <p class="poetheme-palette-active-note">
                <?php
                printf(
                    /* translators: %s: palette name */
                    esc_html__( 'Palette attiva: %s', 'poetheme' ),
                    '<strong>' . esc_html( $palettes[ $active_id ]['name'] ) . '</strong>'
                );
                ?>
                <form class="poetheme-palette-inline" action="<?php echo $post_url; ?>" method="post">
                    <input type="hidden" name="action" value="poetheme_palette_deactivate" />
                    <?php wp_nonce_field( 'poetheme_palette_deactivate' ); ?>
                    <?php submit_button( __( 'Disattiva palette', 'poetheme' ), 'secondary', 'submit', false ); ?>
                </form>
            </p>
        <?php endif; ?>

        <h2><?php esc_html_e( 'Palette disponibili', 'poetheme' ); ?></h2>

        <?php if ( empty( $palettes ) ) : ?>
            <p><?php esc_html_e( 'Nessuna palette importata. Scarica il JSON di esempio, modificalo e importalo per creare la tua prima palette.', 'poetheme' ); ?></p>
        <?php else : ?>
            <div class="poetheme-palette-grid">
                <?php foreach ( $palettes as $id => $palette ) :
                    $is_active   = ( $id === $active_id );
                    $colors      = isset( $palette['colors'] ) ? $palette['colors'] : array();
                    $fonts       = isset( $palette['fonts'] ) ? $palette['fonts'] : array();
                    $global      = isset( $palette['global'] ) ? $palette['global'] : array();
                    $heading     = ! empty( $fonts['heading_font'] ) ? $fonts['heading_font'] : __( 'Predefinito', 'poetheme' );
                    $body        = ! empty( $fonts['body_font'] ) ? $fonts['body_font'] : __( 'Predefinito', 'poetheme' );
                    $width       = isset( $global['site_width'] ) ? (int) $global['site_width'] : 0;
                    $export_url  = wp_nonce_url( admin_url( 'admin-post.php?action=poetheme_palette_export&palette=' . rawurlencode( $id ) ), 'poetheme_palette_export' );
                    ?>
                    <div class="poetheme-palette-card<?php echo $is_active ? ' is-active' : ''; ?>">
                        <div class="poetheme-palette-card__head">
                            <h3><?php echo esc_html( $palette['name'] ); ?></h3>
                            <?php if ( $is_active ) : ?>
                                <span class="poetheme-palette-badge"><?php esc_html_e( 'Attiva', 'poetheme' ); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php poetheme_render_palette_swatches( $colors ); ?>

                        <ul class="poetheme-palette-meta">
                            <li><?php printf( esc_html__( 'Font titoli: %s', 'poetheme' ), esc_html( $heading ) ); ?></li>
                            <li><?php printf( esc_html__( 'Font testo: %s', 'poetheme' ), esc_html( $body ) ); ?></li>
                            <?php if ( $width ) : ?>
                                <li><?php printf( esc_html__( 'Larghezza sito: %dpx', 'poetheme' ), $width ); ?></li>
                            <?php endif; ?>
                        </ul>

                        <div class="poetheme-palette-card__actions">
                            <?php if ( ! $is_active ) : ?>
                                <form class="poetheme-palette-inline" action="<?php echo $post_url; ?>" method="post">
                                    <input type="hidden" name="action" value="poetheme_palette_activate" />
                                    <input type="hidden" name="palette" value="<?php echo esc_attr( $id ); ?>" />
                                    <?php wp_nonce_field( 'poetheme_palette_activate' ); ?>
                                    <?php submit_button( __( 'Applica', 'poetheme' ), 'primary', 'submit', false ); ?>
                                </form>
                            <?php endif; ?>

                            <a class="button" href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Esporta', 'poetheme' ); ?></a>

                            <form class="poetheme-palette-inline" action="<?php echo $post_url; ?>" method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Eliminare questa palette?', 'poetheme' ) ); ?>');">
                                <input type="hidden" name="action" value="poetheme_palette_delete" />
                                <input type="hidden" name="palette" value="<?php echo esc_attr( $id ); ?>" />
                                <?php wp_nonce_field( 'poetheme_palette_delete' ); ?>
                                <?php submit_button( __( 'Elimina', 'poetheme' ), 'delete', 'submit', false ); ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
