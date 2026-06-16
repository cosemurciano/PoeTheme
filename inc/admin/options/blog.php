<?php
/**
 * Blog option defaults, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_get_default_blog_options() {
    return array(
        'list_style' => 'media',
    );
}

/**
 * Retrieve the available footer layout choices.
 *
 * @return array
 */

function poetheme_sanitize_blog_options( $input ) {
    $defaults = poetheme_get_default_blog_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $output = $defaults;

    $list_style = isset( $input['list_style'] ) ? sanitize_key( $input['list_style'] ) : $defaults['list_style'];
    $allowed    = array( 'media', 'cards' );
    if ( in_array( $list_style, $allowed, true ) ) {
        $output['list_style'] = $list_style;
    }

    return $output;
}

/**
 * Sanitize footer options before saving.
 *
 * @param array $input Raw input values.
 * @return array
 */

function poetheme_get_blog_options() {
    $defaults = poetheme_get_default_blog_options();
    $options  = get_option( 'poetheme_blog', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $allowed_styles = array( 'media', 'cards' );
    if ( ! in_array( $options['list_style'], $allowed_styles, true ) ) {
        $options['list_style'] = $defaults['list_style'];
    }

    return $options;
}

/**
 * Register page settings meta box.
 */

function poetheme_render_blog_page() {
    $options      = poetheme_get_blog_options();
    $list_style   = isset( $options['list_style'] ) ? $options['list_style'] : 'media';
    $style_choices = array(
        'media' => __( 'Media list (immagine a sinistra)', 'poetheme' ),
        'cards' => __( 'Cards / griglia', 'poetheme' ),
    );
    ?>
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Blog', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_blog_group' ); ?>

            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Stile listing', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Definisci come mostrare gli articoli nelle liste e nei risultati di ricerca.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <table class="form-table poetheme-fields" role="presentation">
                        <tbody>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Stile elenco articoli', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <fieldset aria-describedby="poetheme-blog-style-help">
                                        <legend class="screen-reader-text"><?php esc_html_e( 'Seleziona lo stile di elenco', 'poetheme' ); ?></legend>
                                        <?php foreach ( $style_choices as $value => $label ) : ?>
                                            <?php $style_id = 'poetheme_blog_style_' . $value; ?>
                                            <label class="poetheme-option-inline" for="<?php echo esc_attr( $style_id ); ?>">
                                                <input id="<?php echo esc_attr( $style_id ); ?>" type="radio" name="poetheme_blog[list_style]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $list_style, $value ); ?> />
                                                <?php echo esc_html( $label ); ?>
                                            </label>
                                            <br />
                                        <?php endforeach; ?>
                                    </fieldset>
                                    <p id="poetheme-blog-style-help" class="description poetheme-field__help"><?php esc_html_e( 'Applica lo stile agli archivi (categorie, tag, autore, data) e ai risultati di ricerca.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the footer settings page.
 */
