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

    // Register a unique @font-face per available font so the live preview can
    // render the actually selected typeface (browsers fetch lazily on use).
    $faces = '';
    foreach ( poetheme_get_available_fonts() as $slug => $font ) {
        if ( empty( $font['url'] ) ) {
            continue;
        }
        $faces .= sprintf(
            "@font-face{font-family:'poetheme-pv-%s';src:url('%s') format('%s');font-display:swap;}\n",
            $slug,
            $font['url'],
            $font['format']
        );
    }
    if ( '' !== $faces ) {
        wp_add_inline_style( 'poetheme-theme-options', $faces );
    }

    wp_enqueue_script( 'poetheme-style-studio', POETHEME_URI . '/assets/js/style-studio.js', array(), poetheme_get_asset_version( 'assets/js/style-studio.js' ), true );
    wp_localize_script(
        'poetheme-style-studio',
        'poethemeStudio',
        array(
            'labels' => array(
                'aaPass'       => __( 'AA', 'poetheme' ),
                'aaFail'       => __( 'Insufficiente', 'poetheme' ),
                'themeDefault' => __( 'Predefinito del tema', 'poetheme' ),
                'menu'         => array( __( 'Home', 'poetheme' ), __( 'Articoli', 'poetheme' ), __( 'Contatti', 'poetheme' ) ),
                'sampleText'   => array(
                    'brand'         => __( 'Brand', 'poetheme' ),
                    'subscribe'     => __( 'Iscriviti', 'poetheme' ),
                    'title'         => __( 'Titolo della pagina di esempio', 'poetheme' ),
                    'meta'          => __( 'Di Redazione · 12 giugno 2026 · 5 min di lettura', 'poetheme' ),
                    'lead'          => __( 'Un paragrafo introduttivo con un ', 'poetheme' ),
                    'link'          => __( 'collegamento', 'poetheme' ),
                    'leadEnd'       => __( ' e del testo in ', 'poetheme' ),
                    'bold'          => __( 'grassetto', 'poetheme' ),
                    'h2'            => __( 'Una sezione importante', 'poetheme' ),
                    'body'          => __( 'Testo di esempio con del codice ', 'poetheme' ),
                    'list'          => array( __( 'Primo punto elenco', 'poetheme' ), __( 'Secondo punto elenco', 'poetheme' ), __( 'Terzo punto elenco', 'poetheme' ) ),
                    'quote'         => __( '“Una citazione che mette in risalto un concetto chiave del contenuto.”', 'poetheme' ),
                    'image'         => __( 'Immagine in evidenza', 'poetheme' ),
                    'caption'       => __( 'Didascalia dell’immagine di esempio.', 'poetheme' ),
                    'h3'            => __( 'Dettagli e dati', 'poetheme' ),
                    'steps'         => array( __( 'Primo passaggio', 'poetheme' ), __( 'Secondo passaggio', 'poetheme' ), __( 'Terzo passaggio', 'poetheme' ) ),
                    'tableHead'     => array( __( 'Piano', 'poetheme' ), __( 'Prezzo', 'poetheme' ) ),
                    'tableRows'     => array( array( __( 'Base', 'poetheme' ), '€9' ), array( __( 'Pro', 'poetheme' ), '€29' ) ),
                    'cta'           => __( 'Azione principale', 'poetheme' ),
                    'secondary'     => __( 'Secondaria', 'poetheme' ),
                    'footerHeading' => __( 'Newsletter', 'poetheme' ),
                ),
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

    $ratios = poetheme_get_studio_ratios();
    $ratio  = isset( $seeds['ratio'] ) ? (string) $seeds['ratio'] : '1.25';
    if ( ! isset( $ratios[ $ratio ] ) ) {
        $ratio = '1.25';
    }

    $base_size = isset( $seeds['base_size'] ) ? (float) $seeds['base_size'] : 1.0;
    $base_size = max( 0.8, min( 1.5, $base_size ) );

    $density = isset( $seeds['density'] ) ? sanitize_key( $seeds['density'] ) : 'comfortable';
    if ( ! in_array( $density, array( 'compact', 'comfortable', 'spacious' ), true ) ) {
        $density = 'comfortable';
    }

    $radius = isset( $seeds['radius'] ) ? (float) $seeds['radius'] : 8.0;
    $radius = max( 0.0, min( 999.0, $radius ) );

    return array(
        'base'           => $base,
        'harmony'        => $harmony,
        'mode'           => $mode,
        'accent_buttons' => ! empty( $seeds['accent_buttons'] ),
        'heading_font'   => isset( $seeds['heading_font'] ) ? sanitize_text_field( (string) $seeds['heading_font'] ) : '',
        'body_font'      => isset( $seeds['body_font'] ) ? sanitize_text_field( (string) $seeds['body_font'] ) : '',
        'base_size'      => $base_size,
        'ratio'          => $ratio,
        'density'        => $density,
        'radius'         => $radius,
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
    $fonts  = isset( $payload['fonts'] ) && is_array( $payload['fonts'] ) ? $payload['fonts'] : array();
    $global = isset( $payload['global'] ) && is_array( $payload['global'] ) ? $payload['global'] : array();
    $seeds  = isset( $payload['seeds'] ) ? poetheme_sanitize_studio_seeds( $payload['seeds'] ) : array();

    $palette = array(
        'name'    => $name,
        'colors'  => poetheme_sanitize_color_options( $colors ),
        'fonts'   => ! empty( $fonts ) ? poetheme_sanitize_font_options( $fonts ) : array(),
        'global'  => ! empty( $global ) ? poetheme_sanitize_palette_global( $global ) : array(),
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
 * Allowed modular-scale ratios (label => factor).
 *
 * @return array<string,float>
 */
function poetheme_get_studio_ratios() {
    return array(
        '1.125' => 1.125,
        '1.2'   => 1.2,
        '1.25'  => 1.25,
        '1.333' => 1.333,
        '1.414' => 1.414,
    );
}

/**
 * Render a grouped (optgroup) font select for the Style Studio.
 *
 * @param string $data_attr     Data attribute hook (without brackets).
 * @param string $default_label Label for the "theme default" option.
 */
function poetheme_studio_render_font_select( $data_attr, $default_label ) {
    $available = function_exists( 'poetheme_get_available_fonts' ) ? poetheme_get_available_fonts() : array();
    ?>
    <select <?php echo esc_attr( $data_attr ); ?> <?php disabled( empty( $available ) ); ?>>
        <option value=""><?php echo esc_html( $default_label ); ?></option>
        <?php foreach ( poetheme_get_font_families( $available ) as $family_label => $family_fonts ) : ?>
            <optgroup label="<?php echo esc_attr( $family_label ); ?>">
                <?php foreach ( $family_fonts as $font ) : ?>
                    <option value="<?php echo esc_attr( $font['slug'] ); ?>" data-font-family="<?php echo esc_attr( $font['family'] ); ?>" data-preview-family="poetheme-pv-<?php echo esc_attr( $font['slug'] ); ?>">
                        <?php echo esc_html( $font['variant_label'] ); ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
    <?php
}

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

                    <hr />
                    <h2 class="poetheme-studio__section"><?php esc_html_e( 'Tipografia', 'poetheme' ); ?></h2>

                    <p class="poetheme-field">
                        <label><strong><?php esc_html_e( 'Font titoli', 'poetheme' ); ?></strong></label><br />
                        <?php poetheme_studio_render_font_select( 'data-studio-heading-font', __( 'Predefinito del tema', 'poetheme' ) ); ?>
                    </p>

                    <p class="poetheme-field">
                        <label><strong><?php esc_html_e( 'Font testo', 'poetheme' ); ?></strong></label><br />
                        <?php poetheme_studio_render_font_select( 'data-studio-body-font', __( 'Predefinito del tema', 'poetheme' ) ); ?>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-base-size"><strong><?php esc_html_e( 'Dimensione base testo (rem)', 'poetheme' ); ?></strong></label><br />
                        <input type="number" id="poetheme-studio-base-size" data-studio-base-size value="1" min="0.8" max="1.5" step="0.05" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-ratio"><strong><?php esc_html_e( 'Scala tipografica', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-ratio" data-studio-ratio>
                            <option value="1.125"><?php esc_html_e( 'Compatta (1.125)', 'poetheme' ); ?></option>
                            <option value="1.2"><?php esc_html_e( 'Terza minore (1.2)', 'poetheme' ); ?></option>
                            <option value="1.25" selected><?php esc_html_e( 'Terza maggiore (1.25)', 'poetheme' ); ?></option>
                            <option value="1.333"><?php esc_html_e( 'Quarta giusta (1.333)', 'poetheme' ); ?></option>
                            <option value="1.414"><?php esc_html_e( 'Ampia (1.414)', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <hr />
                    <h2 class="poetheme-studio__section"><?php esc_html_e( 'Densità e forme', 'poetheme' ); ?></h2>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-density"><strong><?php esc_html_e( 'Densità', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-density" data-studio-density>
                            <option value="compact"><?php esc_html_e( 'Compatta', 'poetheme' ); ?></option>
                            <option value="comfortable" selected><?php esc_html_e( 'Comoda', 'poetheme' ); ?></option>
                            <option value="spacious"><?php esc_html_e( 'Ariosa', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-radius"><strong><?php esc_html_e( 'Arrotondamento pulsanti', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-radius" data-studio-radius>
                            <option value="0"><?php esc_html_e( 'Squadrato', 'poetheme' ); ?></option>
                            <option value="8" selected><?php esc_html_e( 'Morbido', 'poetheme' ); ?></option>
                            <option value="999"><?php esc_html_e( 'Pillola', 'poetheme' ); ?></option>
                        </select>
                    </p>
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
