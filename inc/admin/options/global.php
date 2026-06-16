<?php
/**
 * Global layout option defaults, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_get_default_global_options() {
    return array(
        'layout_mode' => 'full',
        'site_width'  => 1200,
        'background_image_id' => 0,
        'background_position' => 'no-repeat;left top;;',
        'background_size'     => 'auto',
        'enable_media_lightbox' => false,
        'default_font' => '',
        'default_font_fallback' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
    );
}

function poetheme_get_default_spacing_group() {
    $sides = array(
        'top'    => '',
        'right'  => '',
        'bottom' => '',
        'left'   => '',
    );

    return array(
        'margin'  => $sides,
        'padding' => $sides,
    );
}

function poetheme_sanitize_global_options( $input ) {
    $defaults = poetheme_get_default_global_options();

    if ( ! poetheme_user_can_manage_options() ) {
        return poetheme_get_global_options();
    }

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $layout_mode = isset( $input['layout_mode'] ) ? sanitize_key( $input['layout_mode'] ) : $defaults['layout_mode'];
    if ( ! in_array( $layout_mode, array( 'full', 'boxed' ), true ) ) {
        $layout_mode = $defaults['layout_mode'];
    }

    $width = isset( $input['site_width'] ) ? absint( $input['site_width'] ) : $defaults['site_width'];
    $width = max( 960, min( 1920, $width ) );

    $background_image_id = isset( $input['background_image_id'] ) ? absint( $input['background_image_id'] ) : 0;

    $allowed_positions = array(
        '',
        'no-repeat;left top;;',
        'repeat;left top;;',
        'no-repeat;left center;;',
        'repeat;left center;;',
        'no-repeat;left bottom;;',
        'repeat;left bottom;;',
        'no-repeat;center top;;',
        'repeat;center top;;',
        'repeat-x;center top;;',
        'repeat-y;center top;;',
        'no-repeat;center;;',
        'repeat;center;;',
        'no-repeat;center bottom;;',
        'repeat;center bottom;;',
        'repeat-x;center bottom;;',
        'repeat-y;center bottom;;',
        'no-repeat;right top;;',
        'repeat;right top;;',
        'no-repeat;right center;;',
        'repeat;right center;;',
        'no-repeat;right bottom;;',
        'repeat;right bottom;;',
        'no-repeat;center top;fixed;;',
        'no-repeat;center;fixed;cover',
    );

    $background_position = isset( $input['background_position'] ) ? sanitize_text_field( $input['background_position'] ) : $defaults['background_position'];
    if ( ! in_array( $background_position, $allowed_positions, true ) ) {
        $background_position = $defaults['background_position'];
    }

    $allowed_sizes = array( '', 'auto', 'contain', 'cover', 'cover-ultrawide' );
    $background_size = isset( $input['background_size'] ) ? sanitize_text_field( $input['background_size'] ) : $defaults['background_size'];
    if ( ! in_array( $background_size, $allowed_sizes, true ) ) {
        $background_size = $defaults['background_size'];
    }

    $enable_media_lightbox = ! empty( $input['enable_media_lightbox'] );

    $fonts            = poetheme_get_available_fonts();
    $default_font     = '';
    $font_slug        = isset( $input['default_font'] ) ? sanitize_title( $input['default_font'] ) : '';
    if ( $font_slug && isset( $fonts[ $font_slug ] ) ) {
        $default_font = $font_slug;
    }

    $default_fallback = isset( $input['default_font_fallback'] ) ? poetheme_sanitize_font_stack( $input['default_font_fallback'] ) : $defaults['default_font_fallback'];
    if ( '' === $default_fallback ) {
        $default_fallback = $defaults['default_font_fallback'];
    }

    return array(
        'layout_mode' => $layout_mode,
        'site_width'  => $width,
        'background_image_id' => $background_image_id,
        'background_position' => $background_position,
        'background_size'     => $background_size,
        'enable_media_lightbox' => $enable_media_lightbox,
        'default_font' => $default_font,
        'default_font_fallback' => $default_fallback,
    );
}

function poetheme_get_global_options() {
    $defaults = poetheme_get_default_global_options();
    $options  = get_option( 'poetheme_global', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $options['layout_mode'] = in_array( $options['layout_mode'], array( 'full', 'boxed' ), true ) ? $options['layout_mode'] : $defaults['layout_mode'];
    $options['site_width']  = max( 960, min( 1920, absint( $options['site_width'] ) ) );
    $options['background_image_id'] = isset( $options['background_image_id'] ) ? absint( $options['background_image_id'] ) : $defaults['background_image_id'];

    $allowed_positions = array(
        '',
        'no-repeat;left top;;',
        'repeat;left top;;',
        'no-repeat;left center;;',
        'repeat;left center;;',
        'no-repeat;left bottom;;',
        'repeat;left bottom;;',
        'no-repeat;center top;;',
        'repeat;center top;;',
        'repeat-x;center top;;',
        'repeat-y;center top;;',
        'no-repeat;center;;',
        'repeat;center;;',
        'no-repeat;center bottom;;',
        'repeat;center bottom;;',
        'repeat-x;center bottom;;',
        'repeat-y;center bottom;;',
        'no-repeat;right top;;',
        'repeat;right top;;',
        'no-repeat;right center;;',
        'repeat;right center;;',
        'no-repeat;right bottom;;',
        'repeat;right bottom;;',
        'no-repeat;center top;fixed;;',
        'no-repeat;center;fixed;cover',
    );

    if ( ! in_array( $options['background_position'], $allowed_positions, true ) ) {
        $options['background_position'] = $defaults['background_position'];
    }

    $allowed_sizes = array( '', 'auto', 'contain', 'cover', 'cover-ultrawide' );
    if ( ! in_array( $options['background_size'], $allowed_sizes, true ) ) {
        $options['background_size'] = $defaults['background_size'];
    }

    $options['enable_media_lightbox'] = ! empty( $options['enable_media_lightbox'] );

    $fonts = poetheme_get_available_fonts();
    $font_slug = isset( $options['default_font'] ) ? sanitize_title( $options['default_font'] ) : '';
    $options['default_font'] = ( $font_slug && isset( $fonts[ $font_slug ] ) ) ? $font_slug : $defaults['default_font'];

    $fallback = isset( $options['default_font_fallback'] ) ? poetheme_sanitize_font_stack( $options['default_font_fallback'] ) : '';
    if ( '' === $fallback ) {
        $fallback = $defaults['default_font_fallback'];
    }
    $options['default_font_fallback'] = $fallback;

    return $options;
}


/**
 * Render the global settings page.
 */
function poetheme_render_global_page() {
    $options              = poetheme_get_global_options();
    $layout_mode          = isset( $options['layout_mode'] ) ? $options['layout_mode'] : 'full';
    $site_width           = isset( $options['site_width'] ) ? absint( $options['site_width'] ) : 1200;
    $background_image_id  = isset( $options['background_image_id'] ) ? absint( $options['background_image_id'] ) : 0;
    $background_image     = $background_image_id ? wp_get_attachment_image_src( $background_image_id, 'large' ) : false;
    $background_position  = isset( $options['background_position'] ) ? $options['background_position'] : '';
    $background_size      = isset( $options['background_size'] ) ? $options['background_size'] : 'auto';
    $enable_media_lightbox = ! empty( $options['enable_media_lightbox'] );
    $default_font         = isset( $options['default_font'] ) ? $options['default_font'] : '';
    $default_font_fallback = isset( $options['default_font_fallback'] ) ? $options['default_font_fallback'] : '';
    $available_fonts      = poetheme_get_available_fonts();
    $default_font_id      = 'poetheme-global-default-font';
    $default_fallback_id  = 'poetheme-global-default-font-fallback';
    $width_id             = 'poetheme-global-site-width';
    $layout_field         = 'poetheme_global[layout_mode]';
    $background_positions = array(
        ''                              => __( 'Predefinito', 'poetheme' ),
        'no-repeat;left top;;'          => __( 'Sinistra Alto | no-repeat', 'poetheme' ),
        'repeat;left top;;'             => __( 'Sinistra Alto | repeat', 'poetheme' ),
        'no-repeat;left center;;'       => __( 'Sinistra Centro | no-repeat', 'poetheme' ),
        'repeat;left center;;'          => __( 'Sinistra Centro | repeat', 'poetheme' ),
        'no-repeat;left bottom;;'       => __( 'Sinistra Basso | no-repeat', 'poetheme' ),
        'repeat;left bottom;;'          => __( 'Sinistra Basso | repeat', 'poetheme' ),
        'no-repeat;center top;;'        => __( 'Centro Alto | no-repeat', 'poetheme' ),
        'repeat;center top;;'           => __( 'Centro Alto | repeat', 'poetheme' ),
        'repeat-x;center top;;'         => __( 'Centro Alto | repeat-x', 'poetheme' ),
        'repeat-y;center top;;'         => __( 'Centro Alto | repeat-y', 'poetheme' ),
        'no-repeat;center;;'            => __( 'Centro Centro | no-repeat', 'poetheme' ),
        'repeat;center;;'               => __( 'Centro Centro | repeat', 'poetheme' ),
        'no-repeat;center bottom;;'     => __( 'Centro Basso | no-repeat', 'poetheme' ),
        'repeat;center bottom;;'        => __( 'Centro Basso | repeat', 'poetheme' ),
        'repeat-x;center bottom;;'      => __( 'Centro Basso | repeat-x', 'poetheme' ),
        'repeat-y;center bottom;;'      => __( 'Centro Basso | repeat-y', 'poetheme' ),
        'no-repeat;right top;;'         => __( 'Destra Alto | no-repeat', 'poetheme' ),
        'repeat;right top;;'            => __( 'Destra Alto | repeat', 'poetheme' ),
        'no-repeat;right center;;'      => __( 'Destra Centro | no-repeat', 'poetheme' ),
        'repeat;right center;;'         => __( 'Destra Centro | repeat', 'poetheme' ),
        'no-repeat;right bottom;;'      => __( 'Destra Basso | no-repeat', 'poetheme' ),
        'repeat;right bottom;;'         => __( 'Destra Basso | repeat', 'poetheme' ),
        'no-repeat;center top;fixed;;'  => __( 'Centro | no-repeat | fisso', 'poetheme' ),
        'no-repeat;center;fixed;cover'  => __( 'Centro | no-repeat | fisso | copri', 'poetheme' ),
    );

    $background_sizes = array(
        ''               => __( 'Predefinito', 'poetheme' ),
        'auto'           => __( 'Automatico', 'poetheme' ),
        'contain'        => __( 'Contenere', 'poetheme' ),
        'cover'          => __( 'Coprire', 'poetheme' ),
        'cover-ultrawide'=> __( 'Coprire, solo su schermi ultra larghi > 1920px', 'poetheme' ),
    );
    ?>
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Globale', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_global_group' ); ?>
            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Impostazioni globali', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Configura layout, font di default e opzioni di sfondo globali.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <table class="form-table poetheme-fields" role="presentation">
                        <tbody>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Layout', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <fieldset aria-describedby="poetheme-global-layout-help">
                                        <legend class="screen-reader-text"><?php esc_html_e( 'Layout', 'poetheme' ); ?></legend>
                                        <label for="poetheme-global-layout-full">
                                            <input id="poetheme-global-layout-full" type="radio" name="<?php echo esc_attr( $layout_field ); ?>" value="full" <?php checked( 'full', $layout_mode ); ?>>
                                            <?php esc_html_e( 'Larghezza piena (100% della pagina)', 'poetheme' ); ?>
                                        </label>
                                        <br>
                                        <label for="poetheme-global-layout-boxed">
                                            <input id="poetheme-global-layout-boxed" type="radio" name="<?php echo esc_attr( $layout_field ); ?>" value="boxed" <?php checked( 'boxed', $layout_mode ); ?>>
                                            <?php esc_html_e( 'Larghezza box', 'poetheme' ); ?>
                                        </label>
                                        <p id="poetheme-global-layout-help" class="description poetheme-field__help"><?php esc_html_e( 'Scegli come allineare l’intero sito, incluse testata e piè di pagina.', 'poetheme' ); ?></p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr id="poetheme-global-width-row" class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><label for="<?php echo esc_attr( $width_id ); ?>"><?php esc_html_e( 'Larghezza sito (px)', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <input type="number" name="poetheme_global[site_width]" id="<?php echo esc_attr( $width_id ); ?>" value="<?php echo esc_attr( $site_width ); ?>" min="960" max="1920" step="10" class="small-text" aria-describedby="poetheme-global-width-help">
                                    <p id="poetheme-global-width-help" class="description poetheme-field__help"><?php esc_html_e( 'Imposta la larghezza massima del sito per il layout Box. Valori consentiti da 960 a 1920 pixel.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><label for="<?php echo esc_attr( $default_font_id ); ?>"><?php esc_html_e( 'Font globale predefinito', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <select id="<?php echo esc_attr( $default_font_id ); ?>" name="poetheme_global[default_font]" aria-describedby="poetheme-global-default-font-help">
                                        <option value="">&mdash; <?php esc_html_e( 'Usa il font di sistema', 'poetheme' ); ?> &mdash;</option>
                                        <?php foreach ( $available_fonts as $slug => $font_data ) : ?>
                                            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $default_font, $slug ); ?>><?php echo esc_html( $font_data['family'] ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p id="poetheme-global-default-font-help" class="description poetheme-field__help"><?php esc_html_e( 'Questo font verrà usato in tutto il sito se non ne imposti uno specifico nelle sezioni dedicate.', 'poetheme' ); ?></p>
                                    <label class="screen-reader-text" for="<?php echo esc_attr( $default_fallback_id ); ?>"><?php esc_html_e( 'Font alternativi', 'poetheme' ); ?></label>
                                    <input type="text" class="regular-text" id="<?php echo esc_attr( $default_fallback_id ); ?>" name="poetheme_global[default_font_fallback]" value="<?php echo esc_attr( $default_font_fallback ); ?>" placeholder="Arial, Helvetica, sans-serif" aria-describedby="poetheme-global-default-font-fallback-help" />
                                    <p id="poetheme-global-default-font-fallback-help" class="description poetheme-field__help"><?php esc_html_e( 'Elenca i font alternativi da usare se il font principale non è disponibile (esempio: "Arial, Helvetica, sans-serif").', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Lightbox immagini', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <label for="poetheme-enable-media-lightbox">
                                        <input type="checkbox" name="poetheme_global[enable_media_lightbox]" id="poetheme-enable-media-lightbox" value="1" <?php checked( $enable_media_lightbox ); ?> aria-describedby="poetheme-global-lightbox-help">
                                        <?php esc_html_e( 'Apri le immagini dei contenuti in una modale al clic.', 'poetheme' ); ?>
                                    </label>
                                    <p id="poetheme-global-lightbox-help" class="description poetheme-field__help"><?php esc_html_e( 'La modale mostra il file multimediale fino a 1024 pixel di larghezza e supporta anche le gallerie create con l’editor.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Immagine di sfondo della pagina', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <div class="poetheme-background-control" aria-describedby="poetheme-global-background-help">
                                        <div id="poetheme-background-preview" class="poetheme-background-preview">
                                            <?php if ( $background_image ) : ?>
                                                <img src="<?php echo esc_url( $background_image[0] ); ?>" alt="" />
                                            <?php else : ?>
                                                <p class="description"><?php esc_html_e( 'Nessuna immagine selezionata.', 'poetheme' ); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <input type="hidden" id="poetheme_global_background_image_id" name="poetheme_global[background_image_id]" value="<?php echo esc_attr( $background_image_id ); ?>">
                                        <p class="poetheme-background-actions">
                                            <button type="button" class="button button-secondary" id="poetheme-background-upload"><?php esc_html_e( 'Scegli immagine', 'poetheme' ); ?></button>
                                            <button type="button" class="button" id="poetheme-background-remove" <?php disabled( 0 === $background_image_id ); ?>><?php esc_html_e( 'Rimuovi immagine', 'poetheme' ); ?></button>
                                        </p>
                                        <p id="poetheme-global-background-help" class="description poetheme-field__help"><?php esc_html_e( 'Dimensioni consigliate: 1920x1080 px.', 'poetheme' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><label for="poetheme-background-position"><?php esc_html_e( 'Posizione e ripetizione', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <select id="poetheme-background-position" name="poetheme_global[background_position]" aria-describedby="poetheme-global-background-position-help">
                                        <?php foreach ( $background_positions as $value => $label ) : ?>
                                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $background_position ); ?>><?php echo esc_html( $label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p id="poetheme-global-background-position-help" class="description poetheme-field__help"><?php esc_html_e( 'Seleziona la combinazione desiderata di ripetizione, posizione e (se disponibile) fissaggio.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><label for="poetheme-background-size"><?php esc_html_e( 'Dimensione', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <select id="poetheme-background-size" name="poetheme_global[background_size]" aria-describedby="poetheme-global-background-size-help">
                                        <?php foreach ( $background_sizes as $value => $label ) : ?>
                                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $background_size ); ?>><?php echo esc_html( $label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p id="poetheme-global-background-size-help" class="description poetheme-field__help"><?php esc_html_e( 'Questa opzione non è compatibile con la posizione fissa nei browser meno recenti.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function() {
            const layoutRadios = document.querySelectorAll('input[name="<?php echo esc_js( $layout_field ); ?>"]');
            const widthRow = document.getElementById('poetheme-global-width-row');

            function toggleWidthRow() {
                if (!widthRow) {
                    return;
                }
                let selected = 'full';
                layoutRadios.forEach(function(radio) {
                    if (radio.checked) {
                        selected = radio.value;
                    }
                });
                widthRow.style.display = selected === 'boxed' ? '' : 'none';
            }

            layoutRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleWidthRow);
            });

            toggleWidthRow();
        })();
    </script>
    <?php
}
