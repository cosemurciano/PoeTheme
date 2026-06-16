<?php
/**
 * Subheader option defaults, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_get_default_subheader_options() {
    return array(
        'enable_subheader'      => true,
        'show_title'            => true,
        'show_breadcrumbs'      => true,
        'layout'                => 'stack-center',
        'title_tag'             => 'h1',
        'breadcrumbs_separator' => '/',
    );
}

/**
 * Retrieve registered subheader layouts.
 *
 * @return array
 */

function poetheme_get_subheader_layout_choices() {
    return array(
        'stack-center'    => __( 'Titolo e Breadcrumbs centrati (uno sotto l’altro)', 'poetheme' ),
        'stack-left'      => __( 'Titolo e Breadcrumbs a sinistra (uno sotto l’altro)', 'poetheme' ),
        'stack-right'     => __( 'Titolo e Breadcrumbs a destra (uno sotto l’altro)', 'poetheme' ),
        'title-left-only' => __( 'Titolo a sinistra (Breadcrumbs nascosto)', 'poetheme' ),
        'title-right-only'=> __( 'Titolo a destra (Breadcrumbs nascosto)', 'poetheme' ),
        'title-center-only'=> __( 'Titolo al centro (Breadcrumbs nascosto)', 'poetheme' ),
        'split-title-left'=> __( 'Titolo a sinistra e Breadcrumbs a destra su unico rigo (60% / 40%)', 'poetheme' ),
        'split-title-right'=> __( 'Titolo a destra e Breadcrumbs a sinistra su unico rigo (40% / 60%)', 'poetheme' ),
    );
}

/**
 * Retrieve stored subheader options with defaults.
 *
 * @return array
 */

function poetheme_get_subheader_options() {
    $defaults = poetheme_get_default_subheader_options();
    $options  = get_option( 'poetheme_subheader', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $options['enable_subheader'] = ! empty( $options['enable_subheader'] );
    $options['show_title']       = ! empty( $options['show_title'] );
    $options['show_breadcrumbs'] = ! empty( $options['show_breadcrumbs'] );

    $layouts = array_keys( poetheme_get_subheader_layout_choices() );
    if ( ! in_array( $options['layout'], $layouts, true ) ) {
        $options['layout'] = $defaults['layout'];
    }

    $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    if ( ! in_array( strtolower( $options['title_tag'] ), $allowed_tags, true ) ) {
        $options['title_tag'] = $defaults['title_tag'];
    }

    $separator = isset( $options['breadcrumbs_separator'] ) ? (string) $options['breadcrumbs_separator'] : $defaults['breadcrumbs_separator'];
    $separator = wp_strip_all_tags( $separator );
    $separator = trim( $separator );
    if ( '' === $separator ) {
        $separator = $defaults['breadcrumbs_separator'];
    }
    if ( function_exists( 'mb_substr' ) ) {
        $separator = mb_substr( $separator, 0, 10 );
    } else {
        $separator = substr( $separator, 0, 10 );
    }
    $options['breadcrumbs_separator'] = $separator;

    return $options;
}

/**
 * Sanitize subheader options on save.
 *
 * @param array $input Raw values.
 * @return array
 */

function poetheme_sanitize_subheader_options( $input ) {
    $defaults = poetheme_get_default_subheader_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $output = array();

    $output['enable_subheader'] = ! empty( $input['enable_subheader'] );
    $output['show_title']       = ! empty( $input['show_title'] );
    $output['show_breadcrumbs'] = ! empty( $input['show_breadcrumbs'] );

    $layouts = array_keys( poetheme_get_subheader_layout_choices() );
    $layout  = isset( $input['layout'] ) ? sanitize_key( $input['layout'] ) : $defaults['layout'];
    if ( ! in_array( $layout, $layouts, true ) ) {
        $layout = $defaults['layout'];
    }
    $output['layout'] = $layout;

    $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $title_tag     = isset( $input['title_tag'] ) ? strtolower( sanitize_text_field( $input['title_tag'] ) ) : $defaults['title_tag'];
    if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
        $title_tag = $defaults['title_tag'];
    }
    $output['title_tag'] = $title_tag;

    $separator = isset( $input['breadcrumbs_separator'] ) ? wp_strip_all_tags( (string) $input['breadcrumbs_separator'] ) : $defaults['breadcrumbs_separator'];
    $separator = trim( $separator );
    if ( '' === $separator ) {
        $separator = $defaults['breadcrumbs_separator'];
    }
    if ( function_exists( 'mb_substr' ) ) {
        $separator = mb_substr( $separator, 0, 10 );
    } else {
        $separator = substr( $separator, 0, 10 );
    }
    $output['breadcrumbs_separator'] = $separator;

    return $output;
}

/**
 * Sanitize blog options before saving.
 *
 * @param array $input Raw input values.
 * @return array
 */

function poetheme_render_subheader_page() {
    $options      = poetheme_get_subheader_options();
    $layouts      = poetheme_get_subheader_layout_choices();
    $tags         = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $suggested_sep = array( '/', '>', '»', '›', '|' );
    ?>
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Sottointestazione', 'poetheme' ); ?></h1>
        <form action="options.php" method="post" class="poetheme-options-form">
            <?php settings_fields( 'poetheme_subheader_group' ); ?>

            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Visibilità', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Abilita o disabilita rapidamente la sezione sottointestazione.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <table class="form-table poetheme-fields" role="presentation">
                        <tbody>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Mostra sottointestazione', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <label for="poetheme_subheader_enable">
                                        <input type="checkbox" id="poetheme_subheader_enable" name="poetheme_subheader[enable_subheader]" value="1" <?php checked( $options['enable_subheader'] ); ?> aria-describedby="poetheme-subheader-enable-help" />
                                        <?php esc_html_e( 'Abilita la sezione con titolo pagina e breadcrumbs.', 'poetheme' ); ?>
                                    </label>
                                    <p id="poetheme-subheader-enable-help" class="description poetheme-field__help"><?php esc_html_e( 'Se disattivata, titolo e breadcrumbs non verranno mostrati globalmente.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Dettagli sottointestazione', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Gestisci layout, elementi visibili e separatori.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <fieldset id="poetheme-subheader-settings" <?php disabled( ! $options['enable_subheader'] ); ?>>
                        <legend class="screen-reader-text"><?php esc_html_e( 'Impostazioni sottointestazione', 'poetheme' ); ?></legend>
                        <table class="form-table poetheme-fields" role="presentation">
                            <tbody>
                                <tr class="poetheme-field">
                                    <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Elementi visibili', 'poetheme' ); ?></th>
                                    <td class="poetheme-field__control">
                                        <label for="poetheme_subheader_show_title">
                                            <input type="checkbox" id="poetheme_subheader_show_title" name="poetheme_subheader[show_title]" value="1" <?php checked( $options['show_title'] ); ?> aria-describedby="poetheme-subheader-elements-help" />
                                            <?php esc_html_e( 'Mostra il titolo della pagina', 'poetheme' ); ?>
                                        </label>
                                        <br />
                                        <label for="poetheme_subheader_show_breadcrumbs">
                                            <input type="checkbox" id="poetheme_subheader_show_breadcrumbs" name="poetheme_subheader[show_breadcrumbs]" value="1" <?php checked( $options['show_breadcrumbs'] ); ?> aria-describedby="poetheme-subheader-elements-help" />
                                            <?php esc_html_e( 'Mostra i breadcrumbs', 'poetheme' ); ?>
                                        </label>
                                        <p id="poetheme-subheader-elements-help" class="description poetheme-field__help"><?php esc_html_e( 'Le impostazioni della singola pagina possono comunque nascondere questi elementi.', 'poetheme' ); ?></p>
                                    </td>
                                </tr>
                                <tr class="poetheme-field">
                                    <th scope="row" class="poetheme-field__label"><label for="poetheme_subheader_layout"><?php esc_html_e( 'Layout', 'poetheme' ); ?></label></th>
                                    <td class="poetheme-field__control">
                                        <select id="poetheme_subheader_layout" name="poetheme_subheader[layout]" aria-describedby="poetheme-subheader-layout-help">
                                            <?php foreach ( $layouts as $layout_key => $label ) : ?>
                                                <option value="<?php echo esc_attr( $layout_key ); ?>" <?php selected( $options['layout'], $layout_key ); ?>><?php echo esc_html( $label ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p id="poetheme-subheader-layout-help" class="description poetheme-field__help"><?php esc_html_e( 'Scegli come posizionare titolo e breadcrumbs nella sottointestazione.', 'poetheme' ); ?></p>
                                    </td>
                                </tr>
                                <tr class="poetheme-field">
                                    <th scope="row" class="poetheme-field__label"><label for="poetheme_subheader_title_tag"><?php esc_html_e( 'Tag del titolo', 'poetheme' ); ?></label></th>
                                    <td class="poetheme-field__control">
                                        <select id="poetheme_subheader_title_tag" name="poetheme_subheader[title_tag]" aria-describedby="poetheme-subheader-title-help">
                                            <?php foreach ( $tags as $tag ) : ?>
                                                <option value="<?php echo esc_attr( $tag ); ?>" <?php selected( $options['title_tag'], $tag ); ?>><?php echo esc_html( strtoupper( $tag ) ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p id="poetheme-subheader-title-help" class="description poetheme-field__help"><?php esc_html_e( 'Seleziona il tag HTML utilizzato per il titolo principale.', 'poetheme' ); ?></p>
                                    </td>
                                </tr>
                                <tr class="poetheme-field">
                                    <th scope="row" class="poetheme-field__label"><label for="poetheme_subheader_separator"><?php esc_html_e( 'Separatore breadcrumbs', 'poetheme' ); ?></label></th>
                                    <td class="poetheme-field__control">
                                        <input type="text" id="poetheme_subheader_separator" name="poetheme_subheader[breadcrumbs_separator]" value="<?php echo esc_attr( $options['breadcrumbs_separator'] ); ?>" maxlength="10" class="regular-text" list="poetheme-subheader-separators" aria-describedby="poetheme-subheader-separator-help" />
                                        <datalist id="poetheme-subheader-separators">
                                            <?php foreach ( $suggested_sep as $separator ) : ?>
                                                <option value="<?php echo esc_attr( $separator ); ?>"></option>
                                            <?php endforeach; ?>
                                        </datalist>
                                        <p id="poetheme-subheader-separator-help" class="description poetheme-field__help"><?php esc_html_e( 'Suggerimenti: /, >, », ›, | oppure qualsiasi stringa entro 10 caratteri.', 'poetheme' ); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function() {
            const toggle = document.getElementById('poetheme_subheader_enable');
            const fieldset = document.getElementById('poetheme-subheader-settings');

            if (!toggle || !fieldset) {
                return;
            }

            function updateState() {
                fieldset.disabled = !toggle.checked;
            }

            toggle.addEventListener('change', updateState);
            updateState();
        })();
    </script>
    <?php
}

/**
 * Render the blog settings page.
 */
