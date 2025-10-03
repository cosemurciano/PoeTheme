<?php
/**
 * Theme options pages and helpers.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register theme settings.
 */
function poetheme_register_settings() {
    register_setting(
        'poetheme_logo_group',
        'poetheme_logo',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_logo_options',
            'default'           => array(
                'logo_id' => 0,
            ),
        )
    );

    register_setting(
        'poetheme_custom_css_group',
        'poetheme_custom_css',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'poetheme_sanitize_custom_css',
            'default'           => '',
        )
    );
}
add_action( 'admin_init', 'poetheme_register_settings' );

/**
 * Add options pages to the admin menu.
 */
function poetheme_add_options_pages() {
    add_menu_page(
        __( 'PoeTheme', 'poetheme' ),
        __( 'PoeTheme', 'poetheme' ),
        'manage_options',
        'poetheme-settings',
        'poetheme_render_global_page',
        'dashicons-admin-customizer',
        61
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Globale', 'poetheme' ),
        __( 'Globale', 'poetheme' ),
        'manage_options',
        'poetheme-settings',
        'poetheme_render_global_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Logo', 'poetheme' ),
        __( 'Logo', 'poetheme' ),
        'manage_options',
        'poetheme-logo',
        'poetheme_render_logo_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Intestazione', 'poetheme' ),
        __( 'Intestazione', 'poetheme' ),
        'manage_options',
        'poetheme-header',
        'poetheme_render_header_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Sottointestazione', 'poetheme' ),
        __( 'Sottointestazione', 'poetheme' ),
        'manage_options',
        'poetheme-subheader',
        'poetheme_render_subheader_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Piè di pagina', 'poetheme' ),
        __( 'Piè di pagina', 'poetheme' ),
        'manage_options',
        'poetheme-footer',
        'poetheme_render_footer_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Custom CSS', 'poetheme' ),
        __( 'Custom CSS', 'poetheme' ),
        'manage_options',
        'poetheme-custom-css',
        'poetheme_render_custom_css_page'
    );
}
add_action( 'admin_menu', 'poetheme_add_options_pages' );

/**
 * Render the global settings page.
 */
function poetheme_render_global_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Globale', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Le impostazioni globali saranno disponibili a breve.', 'poetheme' ); ?></p>
    </div>
    <?php
}

/**
 * Render the logo settings page.
 */
function poetheme_render_logo_page() {
    $options = poetheme_get_logo_options();
    $logo_id = $options['logo_id'];
    $logo    = $logo_id ? wp_get_attachment_image_src( $logo_id, 'medium' ) : false;
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Logo', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_logo_group' ); ?>
            <div id="poetheme-logo-preview" class="poetheme-logo-preview">
                <?php if ( $logo ) : ?>
                    <img src="<?php echo esc_url( $logo[0] ); ?>" alt="" class="poetheme-logo-preview__image" />
                <?php else : ?>
                    <p class="description"><?php esc_html_e( 'Nessun logo selezionato.', 'poetheme' ); ?></p>
                <?php endif; ?>
            </div>
            <input type="hidden" name="poetheme_logo[logo_id]" id="poetheme_logo_id" value="<?php echo esc_attr( $logo_id ); ?>" />
            <p>
                <button type="button" class="button button-secondary" id="poetheme-logo-upload"><?php esc_html_e( 'Carica logo', 'poetheme' ); ?></button>
                <button type="button" class="button" id="poetheme-logo-remove" <?php disabled( 0 === $logo_id ); ?>><?php esc_html_e( 'Rimuovi logo', 'poetheme' ); ?></button>
            </p>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the header settings page.
 */
function poetheme_render_header_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Intestazione', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( "Le impostazioni dell'intestazione saranno disponibili a breve.", 'poetheme' ); ?></p>
    </div>
    <?php
}

/**
 * Render the subheader settings page.
 */
function poetheme_render_subheader_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Sottointestazione', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Le impostazioni della sottointestazione saranno disponibili a breve.', 'poetheme' ); ?></p>
    </div>
    <?php
}

/**
 * Render the footer settings page.
 */
function poetheme_render_footer_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Piè di pagina', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Le impostazioni del piè di pagina saranno disponibili a breve.', 'poetheme' ); ?></p>
    </div>
    <?php
}

/**
 * Render the custom CSS settings page.
 */
function poetheme_render_custom_css_page() {
    $custom_css = get_option( 'poetheme_custom_css', '' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Custom CSS', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_custom_css_group' ); ?>
            <label class="screen-reader-text" for="poetheme_custom_css"><?php esc_html_e( 'CSS personalizzato', 'poetheme' ); ?></label>
            <textarea id="poetheme_custom_css" name="poetheme_custom_css" rows="20" class="large-text code"><?php echo esc_textarea( $custom_css ); ?></textarea>
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
function poetheme_get_logo_options() {
    $defaults = array(
        'logo_id' => 0,
    );

    $options = get_option( 'poetheme_logo', array() );

    return wp_parse_args( $options, $defaults );
}

/**
 * Sanitize logo options.
 *
 * @param array $input Raw input values.
 * @return array
 */
function poetheme_sanitize_logo_options( $input ) {
    $output = poetheme_get_logo_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    if ( isset( $input['logo_id'] ) ) {
        $output['logo_id'] = absint( $input['logo_id'] );
    }

    return $output;
}

/**
 * Sanitize custom CSS option.
 *
 * @param string $css Raw CSS code.
 * @return string
 */
function poetheme_sanitize_custom_css( $css ) {
    if ( empty( $css ) ) {
        return '';
    }

    $css = (string) $css;

    return wp_kses( $css, array() );
}

/**
 * Enqueue admin assets for the options pages.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_options_admin_assets( $hook ) {
    $screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-logo',
        'poetheme_page_poetheme-header',
        'poetheme_page_poetheme-subheader',
        'poetheme_page_poetheme-footer',
        'poetheme_page_poetheme-custom-css',
    );

    if ( in_array( $hook, $screens, true ) ) {
        wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), POETHEME_VERSION );
    }

    if ( 'poetheme_page_poetheme-logo' === $hook ) {
        wp_enqueue_media();
        wp_enqueue_script( 'poetheme-theme-options', POETHEME_URI . '/assets/js/theme-options.js', array( 'jquery' ), POETHEME_VERSION, true );
        wp_localize_script(
            'poetheme-theme-options',
            'poethemeThemeOptions',
            array(
                'chooseLogo' => __( 'Scegli un logo', 'poetheme' ),
                'selectLogo' => __( 'Usa questo logo', 'poetheme' ),
                'noLogo'     => __( 'Nessun logo selezionato.', 'poetheme' ),
            )
        );
    }

    if ( 'poetheme_page_poetheme-custom-css' === $hook ) {
        $settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

        if ( false !== $settings ) {
            wp_enqueue_script( 'code-editor' );
            wp_enqueue_style( 'code-editor' );
            wp_add_inline_script(
                'code-editor',
                'jQuery(function(){wp.codeEditor.initialize("poetheme_custom_css", ' . wp_json_encode( $settings ) . ');});'
            );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'poetheme_options_admin_assets' );

/**
 * Retrieve legacy theme options with defaults.
 *
 * @return array
 */
function poetheme_get_options() {
    $defaults = array(
        'tagline'            => '',
        'enable_breadcrumbs' => true,
        'custom_logo'        => '',
    );

    $options = get_option( 'poetheme_options', array() );

    return wp_parse_args( $options, $defaults );
}
