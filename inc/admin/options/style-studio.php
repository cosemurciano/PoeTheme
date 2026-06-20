<?php
/**
 * Style Studio (Phase 1: color harmony engine).
 *
 * A client-side generator that derives a full, harmonious color set from a few
 * seeds (brand color, harmony rule, light/dark mode). The result is saved as a
 * style palette (see palettes.php) and can be applied like any other palette.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Allowed harmony rules.
 *
 * @return string[]
 */
function poetheme_get_studio_harmonies() {
    return array( 'complementary', 'analogous', 'triadic', 'split', 'monochromatic' );
}

/**
 * Register the Style Studio submenu.
 */
function poetheme_studio_admin_menu() {
    add_submenu_page(
        'poetheme-settings',
        __( 'Style Studio', 'poetheme' ),
        __( 'Style Studio', 'poetheme' ),
        'manage_options',
        'poetheme-style-studio',
        'poetheme_render_style_studio_page'
    );
}
add_action( 'admin_menu', 'poetheme_studio_admin_menu', 11 );

/**
 * Enqueue Style Studio assets.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_studio_admin_assets( $hook ) {
    if ( 'poetheme_page_poetheme-style-studio' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), poetheme_get_asset_version( 'assets/css/theme-options.css' ) );
    wp_enqueue_script( 'poetheme-style-studio', POETHEME_URI . '/assets/js/style-studio.js', array(), poetheme_get_asset_version( 'assets/js/style-studio.js' ), true );
    wp_localize_script(
        'poetheme-style-studio',
        'poethemeStudio',
        array(
            'labels' => array(
                'aaPass'   => __( 'AA', 'poetheme' ),
                'aaFail'   => __( 'Insufficiente', 'poetheme' ),
                'sample'   => __( 'Titolo di esempio', 'poetheme' ),
                'body'     => __( 'Testo di esempio con un', 'poetheme' ),
                'link'     => __( 'collegamento', 'poetheme' ),
                'cta'      => __( 'Pulsante', 'poetheme' ),
                'menu'     => array( __( 'Home', 'poetheme' ), __( 'Articoli', 'poetheme' ), __( 'Contatti', 'poetheme' ) ),
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'poetheme_studio_admin_assets' );

/**
 * Sanitize the seeds payload from the Style Studio.
 *
 * @param array $seeds Raw seeds.
 * @return array
 */
function poetheme_sanitize_studio_seeds( $seeds ) {
    $seeds = is_array( $seeds ) ? $seeds : array();

    $base = isset( $seeds['base'] ) ? sanitize_hex_color( (string) $seeds['base'] ) : '';
    if ( ! $base ) {
        $base = '#2563eb';
    }

    $harmony = isset( $seeds['harmony'] ) ? sanitize_key( $seeds['harmony'] ) : 'complementary';
    if ( ! in_array( $harmony, poetheme_get_studio_harmonies(), true ) ) {
        $harmony = 'complementary';
    }

    $mode = isset( $seeds['mode'] ) && 'dark' === $seeds['mode'] ? 'dark' : 'light';

    return array(
        'base'           => $base,
        'harmony'        => $harmony,
        'mode'           => $mode,
        'accent_buttons' => ! empty( $seeds['accent_buttons'] ),
    );
}

/**
 * Handle saving a generated palette from the Style Studio.
 */
function poetheme_handle_style_studio_save() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_style_studio_save' );

    $raw     = isset( $_POST['poetheme_studio_payload'] ) ? wp_unslash( $_POST['poetheme_studio_payload'] ) : '';
    $payload = json_decode( (string) $raw, true );

    $redirect = admin_url( 'admin.php?page=poetheme-style-studio' );

    if ( ! is_array( $payload ) ) {
        wp_safe_redirect( add_query_arg( 'poetheme_palette_notice', 'import_invalid', $redirect ) );
        exit;
    }

    $name = isset( $payload['name'] ) ? sanitize_text_field( wp_strip_all_tags( (string) $payload['name'] ) ) : '';
    if ( '' === $name ) {
        $name = __( 'Palette generata', 'poetheme' );
    }

    $colors = isset( $payload['colors'] ) && is_array( $payload['colors'] ) ? $payload['colors'] : array();
    $seeds  = isset( $payload['seeds'] ) ? poetheme_sanitize_studio_seeds( $payload['seeds'] ) : array();

    $palette = array(
        'name'    => $name,
        'colors'  => poetheme_sanitize_color_options( $colors ),
        'fonts'   => array(),
        'global'  => array(),
        'seeds'   => $seeds,
        'created' => time(),
    );

    $palettes = poetheme_get_style_palettes();

    $id = 'pal_' . wp_generate_password( 10, false, false );
    while ( isset( $palettes[ $id ] ) ) {
        $id = 'pal_' . wp_generate_password( 10, false, false );
    }

    $palettes[ $id ] = $palette;
    update_option( 'poetheme_style_palettes', $palettes );

    $apply = ! empty( $_POST['apply'] );
    if ( $apply ) {
        update_option( 'poetheme_active_palette', $id );
    }

    $target = admin_url( 'admin.php?page=poetheme-palette' );
    wp_safe_redirect( add_query_arg( 'poetheme_palette_notice', $apply ? 'activated' : 'imported', $target ) );
    exit;
}
add_action( 'admin_post_poetheme_style_studio_save', 'poetheme_handle_style_studio_save' );

/**
 * Render the Style Studio page.
 */
function poetheme_render_style_studio_page() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    $post_url  = esc_url( admin_url( 'admin-post.php' ) );
    $harmonies = array(
        'complementary' => __( 'Complementare', 'poetheme' ),
        'analogous'     => __( 'Analoga', 'poetheme' ),
        'triadic'       => __( 'Triade', 'poetheme' ),
        'split'         => __( 'Complementare divisa', 'poetheme' ),
        'monochromatic' => __( 'Monocromatica', 'poetheme' ),
    );
    ?>
    <div class="wrap poetheme-options poetheme-studio">
        <h1><?php esc_html_e( 'Style Studio', 'poetheme' ); ?></h1>
        <p class="description">
            <?php esc_html_e( 'Scegli un colore brand e una regola di armonia: il tema genera automaticamente una combinazione cromatica coerente per tutti gli elementi. Vedi l’anteprima dal vivo e i controlli di contrasto, poi salvala come template (gestibile in “Palette e stile”).', 'poetheme' ); ?>
        </p>

        <form action="<?php echo $post_url; ?>" method="post" class="poetheme-studio__form" data-poetheme-studio>
            <input type="hidden" name="action" value="poetheme_style_studio_save" />
            <?php wp_nonce_field( 'poetheme_style_studio_save' ); ?>
            <input type="hidden" name="poetheme_studio_payload" value="" data-studio-payload />

            <div class="poetheme-studio__layout">
                <div class="poetheme-studio__controls">
                    <p class="poetheme-field">
                        <label for="poetheme-studio-name"><strong><?php esc_html_e( 'Nome template', 'poetheme' ); ?></strong></label>
                        <input type="text" id="poetheme-studio-name" name="palette_name" class="regular-text" data-studio-name value="<?php esc_attr_e( 'La mia palette', 'poetheme' ); ?>" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-base"><strong><?php esc_html_e( 'Colore brand', 'poetheme' ); ?></strong></label><br />
                        <input type="color" id="poetheme-studio-base" data-studio-base value="#2563eb" />
                        <input type="text" class="poetheme-studio__hex" data-studio-base-hex value="#2563eb" maxlength="7" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-harmony"><strong><?php esc_html_e( 'Armonia', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-harmony" data-studio-harmony>
                            <?php foreach ( $harmonies as $key => $label ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-mode"><strong><?php esc_html_e( 'Modalità', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-mode" data-studio-mode>
                            <option value="light"><?php esc_html_e( 'Chiara', 'poetheme' ); ?></option>
                            <option value="dark"><?php esc_html_e( 'Scura', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label>
                            <input type="checkbox" data-studio-accent-buttons />
                            <?php esc_html_e( 'Usa il colore accento per i pulsanti', 'poetheme' ); ?>
                        </label>
                    </p>

                    <div class="poetheme-studio__palette" data-studio-swatches aria-hidden="true"></div>
                    <div class="poetheme-studio__contrast" data-studio-contrast></div>
                </div>

                <div class="poetheme-studio__preview" data-studio-preview aria-label="<?php esc_attr_e( 'Anteprima', 'poetheme' ); ?>"></div>
            </div>

            <p class="poetheme-studio__actions">
                <button type="submit" class="button button-secondary" name="apply" value="0"><?php esc_html_e( 'Salva come template', 'poetheme' ); ?></button>
                <button type="submit" class="button button-primary" name="apply" value="1"><?php esc_html_e( 'Salva e applica', 'poetheme' ); ?></button>
            </p>
        </form>
    </div>
    <?php
}
