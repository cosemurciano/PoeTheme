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
}
add_action( 'enqueue_block_editor_assets', 'poetheme_block_editor_assets' );

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

    return $classes;
}
add_filter( 'body_class', 'poetheme_body_classes' );

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
