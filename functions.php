<?php
/**
 * PoeTheme functions and definitions
 *
 * @package PoeTheme
 */

define( 'POETHEME_VERSION', '1.0.0' );

define( 'POETHEME_DIR', get_template_directory() );
define( 'POETHEME_URI', get_template_directory_uri() );

require_once POETHEME_DIR . '/inc/theme-options.php';
require_once POETHEME_DIR . '/inc/template-tags.php';
require_once POETHEME_DIR . '/inc/schema-jsonld.php';
require_once POETHEME_DIR . '/inc/nav-menu.php';

if ( ! function_exists( 'poetheme_setup' ) ) {
    /**
     * Setup theme defaults and registers support for WordPress features.
     */
    function poetheme_setup() {
        load_theme_textdomain( 'poetheme', POETHEME_DIR . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'editor-styles' );
        add_editor_style( 'assets/css/editor.css' );

        register_nav_menus(
            array(
                'primary' => __( 'Primary Menu', 'poetheme' ),
                'top-info' => __( 'Top Info Menu', 'poetheme' ),
                'footer'  => __( 'Footer Menu', 'poetheme' ),
            )
        );

        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            )
        );

        add_theme_support(
            'custom-logo',
            array(
                'height'      => 120,
                'width'       => 480,
                'flex-height' => true,
                'flex-width'  => true,
            )
        );

        add_theme_support( 'customize-selective-refresh-widgets' );
        add_theme_support( 'custom-spacing' );

        add_theme_support( 'editor-color-palette', array() );
        add_theme_support( 'editor-gradient-presets', array() );

        add_theme_support( 'rtl-language-support' );
    }
}
add_action( 'after_setup_theme', 'poetheme_setup' );

/**
 * Register widget areas.
 */
function poetheme_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Sidebar', 'poetheme' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here.', 'poetheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    register_sidebar(
        array(
            'name'          => __( 'Footer Widgets', 'poetheme' ),
            'id'            => 'footer-1',
            'description'   => __( 'Appears in the footer widget area.', 'poetheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    register_sidebar(
        array(
            'name'          => __( 'Page Widgets', 'poetheme' ),
            'id'            => 'page-widgets',
            'description'   => __( 'Widgets displayed in page templates with a sidebar.', 'poetheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'poetheme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function poetheme_scripts() {
    wp_enqueue_style( 'poetheme-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), POETHEME_VERSION );
    wp_enqueue_style( 'poetheme-style', get_stylesheet_uri(), array( 'poetheme-tailwind' ), POETHEME_VERSION );

    wp_enqueue_script( 'poetheme-alpine', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), POETHEME_VERSION, true );
    wp_script_add_data( 'poetheme-alpine', 'defer', true );

    wp_enqueue_script( 'poetheme-lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js', array(), POETHEME_VERSION, true );
    wp_script_add_data( 'poetheme-lucide', 'defer', true );

    wp_add_inline_script(
        'poetheme-lucide',
        'document.addEventListener("DOMContentLoaded",function(){if(window.lucide){window.lucide.createIcons();}});'
    );

    wp_enqueue_script( 'poetheme-navigation', POETHEME_URI . '/assets/js/navigation.js', array(), POETHEME_VERSION, true );
    wp_script_add_data( 'poetheme-navigation', 'defer', true );
}
add_action( 'wp_enqueue_scripts', 'poetheme_scripts' );

/**
 * Block editor assets.
 */
function poetheme_block_editor_assets() {
    wp_enqueue_style( 'poetheme-editor-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), POETHEME_VERSION );
    wp_enqueue_style( 'poetheme-editor-style', POETHEME_URI . '/assets/css/editor.css', array( 'poetheme-editor-tailwind' ), POETHEME_VERSION );
    wp_enqueue_script( 'poetheme-editor-alpine', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), POETHEME_VERSION, true );
    wp_script_add_data( 'poetheme-editor-alpine', 'defer', true );

    $font_styles = poetheme_prepare_font_styles();

    if ( ! empty( $font_styles['font_faces'] ) || ! empty( $font_styles['css_rules'] ) ) {
        wp_add_inline_style( 'poetheme-editor-style', $font_styles['font_faces'] . $font_styles['css_rules'] );
    }
}
add_action( 'enqueue_block_editor_assets', 'poetheme_block_editor_assets' );

/**
 * Output custom fonts selected within the theme options.
 */
function poetheme_output_font_settings() {
    $font_styles = poetheme_prepare_font_styles();

    if ( empty( $font_styles['font_faces'] ) && empty( $font_styles['css_rules'] ) ) {
        return;
    }

    echo '<style id="poetheme-font-settings">' . $font_styles['font_faces'] . $font_styles['css_rules'] . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'poetheme_output_font_settings', 85 );

/**
 * Output custom CSS defined in the theme options.
 */
function poetheme_output_custom_css() {
    $custom_css = get_option( 'poetheme_custom_css', '' );

    if ( empty( $custom_css ) ) {
        return;
    }

    $custom_css = trim( wp_kses( $custom_css, array() ) );

    if ( '' === $custom_css ) {
        return;
    }

    echo '<style id="poetheme-custom-css">' . $custom_css . '</style>';
}
add_action( 'wp_head', 'poetheme_output_custom_css', 120 );

/**
 * Output layout CSS variables based on global settings.
 */
function poetheme_output_layout_settings() {
    $options = poetheme_get_global_options();
    $width   = isset( $options['site_width'] ) ? absint( $options['site_width'] ) : 1200;
    $width   = max( 960, min( 1920, $width ) );

    printf( '<style id="poetheme-layout-settings">:root{--poetheme-site-width:%dpx;}</style>', $width );
}
add_action( 'wp_head', 'poetheme_output_layout_settings', 90 );

function poetheme_output_design_settings() {
    $global_options      = poetheme_get_global_options();
    $color_options       = poetheme_get_color_options();
    $raw_color_options   = get_option( 'poetheme_colors', array() );
    $header_color_defined = is_array( $raw_color_options ) && array_key_exists( 'header_background_color', $raw_color_options );
    $styles         = '';
    $body_rule      = 'body.poetheme-has-color-settings';
    $body_css       = array();

    $page_background = ! empty( $color_options['page_background_color'] ) ? $color_options['page_background_color'] : '#f9fafb';
    $body_css[]      = 'background-color:' . esc_attr( $page_background ) . ' !important';

    $background_image_id = isset( $global_options['background_image_id'] ) ? absint( $global_options['background_image_id'] ) : 0;
    $background_position = isset( $global_options['background_position'] ) ? $global_options['background_position'] : '';
    $background_size_opt = isset( $global_options['background_size'] ) ? $global_options['background_size'] : 'auto';
    $background_url      = $background_image_id ? wp_get_attachment_image_url( $background_image_id, 'full' ) : '';

    if ( $background_url ) {
        $body_css[] = 'background-image:url(' . esc_url_raw( $background_url ) . ')';

        $parts = array_pad( explode( ';', $background_position ), 4, '' );
        $repeat     = $parts[0];
        $position   = $parts[1];
        $attachment = $parts[2];
        $size_from_position = $parts[3];

        if ( $repeat ) {
            $body_css[] = 'background-repeat:' . esc_attr( $repeat );
        }

        if ( $position ) {
            $body_css[] = 'background-position:' . esc_attr( $position );
        }

        if ( $attachment ) {
            $body_css[] = 'background-attachment:' . esc_attr( $attachment );
        }

        $size_value = '';
        if ( $background_size_opt && 'cover-ultrawide' !== $background_size_opt ) {
            $size_value = $background_size_opt;
        } elseif ( ! $background_size_opt && $size_from_position ) {
            $size_value = $size_from_position;
        } elseif ( $background_size_opt && 'cover-ultrawide' === $background_size_opt ) {
            $size_value = 'auto';
        }

        if ( $size_value ) {
            $body_css[] = 'background-size:' . esc_attr( $size_value );
        }

        if ( 'cover-ultrawide' === $background_size_opt ) {
            $styles .= '@media (min-width:1921px){' . $body_rule . '{background-size:cover;}}';
        }
    } else {
        $body_css[] = 'background-image:none';
    }

    $header_background = ! empty( $color_options['header_background_color'] ) ? $color_options['header_background_color'] : '#ffffff';

    if ( ! empty( $color_options['header_background_transparent'] ) ) {
        $header_background = 'transparent';
    }

    $header_shadow_default = '0 1px 2px rgba(15, 23, 42, 0.08)';
    $header_shadow_value   = ! empty( $color_options['header_disable_shadow'] ) ? 'none' : $header_shadow_default;

    $css_variables = array(
        '--poetheme-content-text-color'        => ! empty( $color_options['content_text_color'] ) ? $color_options['content_text_color'] : '#111827',
        '--poetheme-content-link-color'        => ! empty( $color_options['content_link_color'] ) ? $color_options['content_link_color'] : '#2563eb',
        '--poetheme-content-link-decoration'   => ! empty( $color_options['content_link_underline'] ) ? 'underline' : 'none',
        '--poetheme-content-strong-color'      => ! empty( $color_options['content_strong_color'] ) ? $color_options['content_strong_color'] : '#111827',
        '--poetheme-content-background-color'  => ! empty( $color_options['content_background_color'] ) ? $color_options['content_background_color'] : '#ffffff',
        '--poetheme-header-background-color'   => $header_background,
        '--poetheme-header-shadow'             => $header_shadow_value,
        '--poetheme-menu-link-color'           => ! empty( $color_options['menu_link_color'] ) ? $color_options['menu_link_color'] : '#374151',
        '--poetheme-menu-link-background'      => ! empty( $color_options['menu_link_background_color'] ) ? $color_options['menu_link_background_color'] : 'transparent',
        '--poetheme-menu-active-link-color'    => ! empty( $color_options['menu_active_link_color'] ) ? $color_options['menu_active_link_color'] : '#2563eb',
        '--poetheme-menu-active-background'    => ! empty( $color_options['menu_active_link_background'] ) ? $color_options['menu_active_link_background'] : 'transparent',
        '--poetheme-cta-background-color'      => ! empty( $color_options['cta_background_color'] ) ? $color_options['cta_background_color'] : '#2563eb',
        '--poetheme-cta-text-color'            => ! empty( $color_options['cta_text_color'] ) ? $color_options['cta_text_color'] : '#ffffff',
        '--poetheme-top-bar-background'        => ! empty( $color_options['top_bar_background_color'] ) ? $color_options['top_bar_background_color'] : '#111827',
        '--poetheme-top-bar-icon-color'        => ! empty( $color_options['top_bar_icon_color'] ) ? $color_options['top_bar_icon_color'] : '#ffffff',
        '--poetheme-top-bar-text-color'        => ! empty( $color_options['top_bar_text_color'] ) ? $color_options['top_bar_text_color'] : '#ffffff',
        '--poetheme-top-bar-link-color'        => ! empty( $color_options['top_bar_link_color'] ) ? $color_options['top_bar_link_color'] : '#ffffff',
        '--poetheme-general-link-color'        => ! empty( $color_options['general_link_color'] ) ? $color_options['general_link_color'] : '#2563eb',
        '--poetheme-heading-h1-color'          => ! empty( $color_options['heading_h1_color'] ) ? $color_options['heading_h1_color'] : '#111827',
        '--poetheme-heading-h1-background'     => ! empty( $color_options['heading_h1_background'] ) ? $color_options['heading_h1_background'] : 'transparent',
        '--poetheme-heading-h2-color'          => ! empty( $color_options['heading_h2_color'] ) ? $color_options['heading_h2_color'] : '#111827',
        '--poetheme-heading-h2-background'     => ! empty( $color_options['heading_h2_background'] ) ? $color_options['heading_h2_background'] : 'transparent',
        '--poetheme-heading-h3-color'          => ! empty( $color_options['heading_h3_color'] ) ? $color_options['heading_h3_color'] : '#111827',
        '--poetheme-heading-h3-background'     => ! empty( $color_options['heading_h3_background'] ) ? $color_options['heading_h3_background'] : 'transparent',
        '--poetheme-heading-h4-color'          => ! empty( $color_options['heading_h4_color'] ) ? $color_options['heading_h4_color'] : '#111827',
        '--poetheme-heading-h4-background'     => ! empty( $color_options['heading_h4_background'] ) ? $color_options['heading_h4_background'] : 'transparent',
        '--poetheme-heading-h5-color'          => ! empty( $color_options['heading_h5_color'] ) ? $color_options['heading_h5_color'] : '#111827',
        '--poetheme-heading-h5-background'     => ! empty( $color_options['heading_h5_background'] ) ? $color_options['heading_h5_background'] : 'transparent',
        '--poetheme-heading-h6-color'          => ! empty( $color_options['heading_h6_color'] ) ? $color_options['heading_h6_color'] : '#111827',
        '--poetheme-heading-h6-background'     => ! empty( $color_options['heading_h6_background'] ) ? $color_options['heading_h6_background'] : 'transparent',
    );

    $body_css[] = implode( ';', array_map( function ( $key, $value ) {
        return $key . ':' . esc_attr( $value );
    }, array_keys( $css_variables ), $css_variables ) );

    $styles .= $body_rule . '{' . implode( ';', $body_css ) . ';}';

    $styles .= 'body.poetheme-has-color-settings a{color:var(--poetheme-general-link-color) !important;}';

    $styles .= 'body.poetheme-has-color-settings main{color:var(--poetheme-content-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings main p,body.poetheme-has-color-settings main li,body.poetheme-has-color-settings main span,body.poetheme-has-color-settings main td,body.poetheme-has-color-settings main th,body.poetheme-has-color-settings main dd,body.poetheme-has-color-settings main dt{color:var(--poetheme-content-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings main strong,body.poetheme-has-color-settings main b{color:var(--poetheme-content-strong-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings main a{color:var(--poetheme-content-link-color) !important;text-decoration:var(--poetheme-content-link-decoration) !important;}';
    $styles .= 'body.poetheme-has-color-settings main a:hover,body.poetheme-has-color-settings main a:focus{color:var(--poetheme-content-link-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings main,body.poetheme-has-color-settings main .poetheme-container,body.poetheme-has-color-settings main article,body.poetheme-has-color-settings main .widget,body.poetheme-has-color-settings main .comment-body{background-color:var(--poetheme-content-background-color) !important;}';

    $styles .= 'body.poetheme-has-color-settings .poetheme-site-header{background-color:var(--poetheme-header-background-color) !important;box-shadow:var(--poetheme-header-shadow) !important;}';
    if ( ! empty( $color_options['header_background_transparent'] ) || ( $header_color_defined && ! empty( $color_options['header_background_color'] ) ) ) {
        $styles .= 'body.poetheme-has-color-settings .poetheme-site-header{background-image:none !important;}';
    }
    $styles .= 'body.poetheme-has-color-settings .poetheme-nav--location-primary a{color:var(--poetheme-menu-link-color) !important;background-color:var(--poetheme-menu-link-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-nav--location-primary .current-menu-item > a,body.poetheme-has-color-settings .poetheme-nav--location-primary .current_page_item > a,body.poetheme-has-color-settings .poetheme-nav--location-primary a:hover,body.poetheme-has-color-settings .poetheme-nav--location-primary a:focus{color:var(--poetheme-menu-active-link-color) !important;background-color:var(--poetheme-menu-active-background) !important;}';

    $styles .= 'body.poetheme-has-color-settings .poetheme-cta-button{background-color:var(--poetheme-cta-background-color) !important;color:var(--poetheme-cta-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-cta-button:hover,body.poetheme-has-color-settings .poetheme-cta-button:focus{background-color:var(--poetheme-cta-background-color) !important;color:var(--poetheme-cta-text-color) !important;}';

    $styles .= 'body.poetheme-has-color-settings .poetheme-top-bar{background-color:var(--poetheme-top-bar-background) !important;color:var(--poetheme-top-bar-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-top-bar,body.poetheme-has-color-settings .poetheme-top-bar p,body.poetheme-has-color-settings .poetheme-top-bar span{color:var(--poetheme-top-bar-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-top-bar a{color:var(--poetheme-top-bar-link-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-top-bar a:hover,body.poetheme-has-color-settings .poetheme-top-bar a:focus{color:var(--poetheme-top-bar-link-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-top-bar i[data-lucide]{color:var(--poetheme-top-bar-icon-color) !important;}';

    $styles .= 'body.poetheme-has-color-settings main h1{color:var(--poetheme-heading-h1-color) !important;background-color:var(--poetheme-heading-h1-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h2{color:var(--poetheme-heading-h2-color) !important;background-color:var(--poetheme-heading-h2-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h3{color:var(--poetheme-heading-h3-color) !important;background-color:var(--poetheme-heading-h3-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h4{color:var(--poetheme-heading-h4-color) !important;background-color:var(--poetheme-heading-h4-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h5{color:var(--poetheme-heading-h5-color) !important;background-color:var(--poetheme-heading-h5-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h6{color:var(--poetheme-heading-h6-color) !important;background-color:var(--poetheme-heading-h6-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings main h1 a,body.poetheme-has-color-settings main h2 a,body.poetheme-has-color-settings main h3 a,body.poetheme-has-color-settings main h4 a,body.poetheme-has-color-settings main h5 a,body.poetheme-has-color-settings main h6 a{color:inherit !important;}';

    if ( $styles ) {
        echo '<style id="poetheme-design-settings">' . $styles . '</style>';
    }
}
add_action( 'wp_head', 'poetheme_output_design_settings', 95 );

/**
 * Add body classes.
 */
function poetheme_body_classes( $classes ) {
    if ( is_rtl() ) {
        $classes[] = 'rtl';
    }

    $layout_options = poetheme_get_global_options();
    if ( isset( $layout_options['layout_mode'] ) ) {
        $classes[] = 'poetheme-layout-' . sanitize_html_class( $layout_options['layout_mode'] );
    }

    $classes[] = 'poetheme-has-color-settings';

    return $classes;
}
add_filter( 'body_class', 'poetheme_body_classes' );

/**
 * Add a body class when custom fonts are active.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function poetheme_font_body_class( $classes ) {
    $font_styles = poetheme_prepare_font_styles();

    if ( ! empty( $font_styles['used_fonts'] ) ) {
        $classes[] = 'poetheme-has-font-settings';
    }

    return $classes;
}
add_filter( 'body_class', 'poetheme_font_body_class' );

/**
 * Ensure images have alt text.
 */
function poetheme_attachment_alt_text( $attr, $attachment ) {
    if ( empty( $attr['alt'] ) ) {
        $attr['alt'] = get_the_title( $attachment );
    }

    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'poetheme_attachment_alt_text', 10, 2 );

/**
 * Add skip link target support.
 */
function poetheme_skip_link_target() {
    echo '<a class="skip-link" href="#primary-content">' . esc_html__( 'Skip to content', 'poetheme' ) . '</a>';
}
add_action( 'poetheme_before_header', 'poetheme_skip_link_target' );
