<?php
/**
 * Custom CSS admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_render_custom_css_page() {
    $custom_css = get_option( 'poetheme_custom_css', '' );
    ?>
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Custom CSS', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_custom_css_group' ); ?>
            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Editor CSS', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Inserisci qui regole CSS personalizzate. Il codice verrà iniettato nell’head del tema.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <label class="screen-reader-text" for="poetheme_custom_css"><?php esc_html_e( 'CSS personalizzato', 'poetheme' ); ?></label>
                    <textarea id="poetheme_custom_css" name="poetheme_custom_css" rows="20" class="large-text code poetheme-code-editor" aria-describedby="poetheme-custom-css-help"><?php echo esc_textarea( $custom_css ); ?></textarea>
                    <p id="poetheme-custom-css-help" class="description poetheme-field__help"><?php esc_html_e( 'Usa solo CSS valido. Questo contenuto è visibile solo agli amministratori.', 'poetheme' ); ?></p>
                </div>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Retrieve logo options with defaults.
 *
 * @return array
 */
