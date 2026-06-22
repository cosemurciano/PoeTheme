<?php
/**
 * Settings registration, admin menu, and shared option helpers.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_register_settings() {
    register_setting(
        'poetheme_global_group',
        'poetheme_global',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_global_options',
            'default'           => poetheme_get_default_global_options(),
        )
    );

    register_setting(
        'poetheme_colors_group',
        'poetheme_colors',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_color_options',
            'default'           => poetheme_get_default_color_options(),
        )
    );

    register_setting(
        'poetheme_fonts_group',
        'poetheme_fonts',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_font_options',
            'default'           => poetheme_get_default_font_options(),
        )
    );

    register_setting(
        'poetheme_logo_group',
        'poetheme_logo',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_logo_options',
            'default'           => poetheme_get_default_logo_options(),
        )
    );

    register_setting(
        'poetheme_subheader_group',
        'poetheme_subheader',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_subheader_options',
            'default'           => poetheme_get_default_subheader_options(),
        )
    );

    register_setting(
        'poetheme_blog_group',
        'poetheme_blog',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_blog_options',
            'default'           => poetheme_get_default_blog_options(),
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

    register_setting(
        'poetheme_header_group',
        'poetheme_header',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_header_options',
            'default'           => poetheme_get_default_header_options(),
        )
    );

    register_setting(
        'poetheme_footer_group',
        'poetheme_footer',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_footer_options',
            'default'           => poetheme_get_default_footer_options(),
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
        __( 'Gestione Colori', 'poetheme' ),
        __( 'Gestione Colori', 'poetheme' ),
        'manage_options',
        'poetheme-colors',
        'poetheme_render_colors_page'
    );

    add_submenu_page(
        'poetheme-settings',
        __( 'Gestione Font', 'poetheme' ),
        __( 'Gestione Font', 'poetheme' ),
        'manage_options',
        'poetheme-fonts',
        'poetheme_render_fonts_page'
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
        __( 'Blog', 'poetheme' ),
        __( 'Blog', 'poetheme' ),
        'manage_options',
        'poetheme-blog',
        'poetheme_render_blog_page'
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

    add_submenu_page(
        'poetheme-settings',
        __( 'SEO Schema', 'poetheme' ),
        __( 'SEO Schema', 'poetheme' ),
        'manage_options',
        'poetheme-seo-schema',
        'poetheme_schema_render_options_page'
    );
}
add_action( 'admin_menu', 'poetheme_add_options_pages' );

/**
 * Reorder the PoeTheme submenu into a more meaningful flow.
 *
 * Runs late (priority 999) so it also covers pages registered by other modules
 * (Style Studio, palettes). Style Studio and the palette manager are grouped
 * right after the global settings; the remaining items keep a logical grouping
 * (granular managers, structural areas, then advanced tools).
 */
function poetheme_reorder_admin_submenu() {
    global $submenu;

    if ( empty( $submenu['poetheme-settings'] ) ) {
        return;
    }

    $order = array(
        'poetheme-settings',
        'poetheme-palette',
        'poetheme-logo',
        'poetheme-header',
        'poetheme-subheader',
        'poetheme-blog',
        'poetheme-footer',
        'poetheme-custom-css',
        'poetheme-seo-schema',
    );

    // Colors and Fonts are managed by Style Studio (their pages redirect there).
    // Style Studio itself is reachable only from the "Palette e stile" gallery
    // (Create / Edit), so its menu entry is hidden too.
    $hidden = array( 'poetheme-colors', 'poetheme-fonts', 'poetheme-style-studio' );

    $by_slug = array();
    foreach ( $submenu['poetheme-settings'] as $item ) {
        if ( isset( $item[2] ) ) {
            $by_slug[ $item[2] ] = $item;
        }
    }

    foreach ( $hidden as $slug ) {
        unset( $by_slug[ $slug ] );
    }

    $sorted = array();
    foreach ( $order as $slug ) {
        if ( isset( $by_slug[ $slug ] ) ) {
            $sorted[] = $by_slug[ $slug ];
            unset( $by_slug[ $slug ] );
        }
    }

    // Append any pages not listed above so nothing disappears.
    foreach ( $by_slug as $item ) {
        $sorted[] = $item;
    }

    $submenu['poetheme-settings'] = array_values( $sorted );
}
add_action( 'admin_menu', 'poetheme_reorder_admin_submenu', 999 );

/**
 * Redirect the legacy Colors/Fonts pages to Style Studio.
 *
 * Colors, fonts and sizes are now configured from Style Studio (palettes). The
 * pages stay registered so their settings/options keep working, but any direct
 * visit is sent to Style Studio.
 */
function poetheme_redirect_legacy_style_pages() {
    if ( ! is_admin() ) {
        return;
    }

    $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

    if ( in_array( $page, array( 'poetheme-colors', 'poetheme-fonts' ), true ) ) {
        wp_safe_redirect( admin_url( 'admin.php?page=poetheme-style-studio' ) );
        exit;
    }
}
add_action( 'admin_init', 'poetheme_redirect_legacy_style_pages' );

function poetheme_options_admin_assets( $hook ) {
    $style_screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-settings',
        'poetheme_page_poetheme-colors',
        'poetheme_page_poetheme-fonts',
        'poetheme_page_poetheme-logo',
        'poetheme_page_poetheme-header',
        'poetheme_page_poetheme-subheader',
        'poetheme_page_poetheme-blog',
        'poetheme_page_poetheme-footer',
        'poetheme_page_poetheme-custom-css',
    );

    if ( in_array( $hook, $style_screens, true ) ) {
        wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), poetheme_get_asset_version( 'assets/css/theme-options.css' ) );
    }

    $script_screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-settings',
        'poetheme_page_poetheme-colors',
        'poetheme_page_poetheme-logo',
        'poetheme_page_poetheme-blog',
        'poetheme_page_poetheme-footer',
    );

    if ( in_array( $hook, $script_screens, true ) ) {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'poetheme-theme-options', POETHEME_URI . '/assets/js/theme-options.js', array( 'jquery', 'wp-color-picker' ), poetheme_get_asset_version( 'assets/js/theme-options.js' ), true );
        wp_localize_script(
            'poetheme-theme-options',
            'poethemeThemeOptions',
            array(
                'chooseLogo'          => __( 'Scegli un logo', 'poetheme' ),
                'selectLogo'          => __( 'Usa questo logo', 'poetheme' ),
                'noLogo'              => __( 'Nessun logo selezionato.', 'poetheme' ),
                'chooseBackground'    => __( 'Scegli un’immagine di sfondo', 'poetheme' ),
                'selectBackground'    => __( 'Usa questa immagine', 'poetheme' ),
                'noBackground'        => __( 'Nessuna immagine selezionata.', 'poetheme' ),
                'alphaLabel'          => __( 'Trasparenza', 'poetheme' ),
                'alphaSuffix'         => '%',
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

function poetheme_get_default_page_settings() {
    return array(
        'hide_breadcrumbs'   => false,
        'hide_title'         => false,
        'remove_top_padding' => false,
    );
}


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

/**
 * Retrieve header options with defaults.
 *
 * @return array
 */
