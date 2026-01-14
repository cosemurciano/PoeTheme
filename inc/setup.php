<?php
/**
 * Theme setup and global hooks.
 *
 * Responsibility: register theme supports/menus and global filters for front-end behavior.
 * It must NOT enqueue assets or output <head> CSS/JS.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
                'primary'  => __( 'Primary Menu', 'poetheme' ),
                'top-info' => __( 'Top Info Menu', 'poetheme' ),
                'footer'   => __( 'Footer Menu', 'poetheme' ),
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
 * Add body classes.
 *
 * @param array $classes Existing classes.
 * @return array
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
 *
 * @param array $attr       Image attributes.
 * @param int   $attachment Attachment post ID.
 * @return array
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
