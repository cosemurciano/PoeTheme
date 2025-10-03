<?php
/**
 * Theme options page.
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
    register_setting( 'poetheme_options', 'poetheme_options', 'poetheme_sanitize_options' );

    add_settings_section(
        'poetheme_general_section',
        __( 'General', 'poetheme' ),
        '__return_false',
        'poetheme_options'
    );

    add_settings_field(
        'poetheme_tagline',
        __( 'Alternative Tagline', 'poetheme' ),
        'poetheme_field_tagline',
        'poetheme_options',
        'poetheme_general_section'
    );

    add_settings_field(
        'poetheme_enable_breadcrumbs',
        __( 'Enable Breadcrumbs', 'poetheme' ),
        'poetheme_field_enable_breadcrumbs',
        'poetheme_options',
        'poetheme_general_section'
    );

    add_settings_section(
        'poetheme_logo_section',
        __( 'Logo', 'poetheme' ),
        '__return_false',
        'poetheme_options'
    );

    add_settings_field(
        'poetheme_custom_logo',
        __( 'Custom Logo URL', 'poetheme' ),
        'poetheme_field_custom_logo',
        'poetheme_options',
        'poetheme_logo_section'
    );
}
add_action( 'admin_init', 'poetheme_register_settings' );

/**
 * Add options page to admin menu.
 */
function poetheme_add_options_page() {
    add_theme_page(
        __( 'PoeTheme Options', 'poetheme' ),
        __( 'PoeTheme Options', 'poetheme' ),
        'manage_options',
        'poetheme-options',
        'poetheme_render_options_page'
    );
}
add_action( 'admin_menu', 'poetheme_add_options_page' );

/**
 * Sanitize options.
 *
 * @param array $input Input values.
 * @return array
 */
function poetheme_sanitize_options( $input ) {
    $output = poetheme_get_options();

    if ( isset( $input['tagline'] ) ) {
        $output['tagline'] = sanitize_text_field( $input['tagline'] );
    }

    $output['enable_breadcrumbs'] = isset( $input['enable_breadcrumbs'] ) ? (bool) $input['enable_breadcrumbs'] : false;

    if ( isset( $input['custom_logo'] ) ) {
        $output['custom_logo'] = esc_url_raw( $input['custom_logo'] );
    }

    return $output;
}

/**
 * Retrieve theme options with defaults.
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

/**
 * Render tagline field.
 */
function poetheme_field_tagline() {
    $options = poetheme_get_options();
    ?>
    <input type="text" id="poetheme_tagline" name="poetheme_options[tagline]" value="<?php echo esc_attr( $options['tagline'] ); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e( 'Overrides the site tagline for meta descriptions.', 'poetheme' ); ?></p>
    <?php
}

/**
 * Render breadcrumbs toggle field.
 */
function poetheme_field_enable_breadcrumbs() {
    $options = poetheme_get_options();
    ?>
    <label for="poetheme_enable_breadcrumbs">
        <input type="checkbox" id="poetheme_enable_breadcrumbs" name="poetheme_options[enable_breadcrumbs]" value="1" <?php checked( $options['enable_breadcrumbs'], true ); ?> />
        <?php esc_html_e( 'Display breadcrumbs on archive and singular pages.', 'poetheme' ); ?>
    </label>
    <?php
}

/**
 * Render custom logo field.
 */
function poetheme_field_custom_logo() {
    $options = poetheme_get_options();
    ?>
    <input type="url" id="poetheme_custom_logo" name="poetheme_options[custom_logo]" value="<?php echo esc_attr( $options['custom_logo'] ); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e( 'Paste the URL of the logo image if you prefer not to use the Customizer logo.', 'poetheme' ); ?></p>
    <?php
}

/**
 * Render options page markup.
 */
function poetheme_render_options_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'PoeTheme Options', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'poetheme_options' );
            do_settings_sections( 'poetheme_options' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
