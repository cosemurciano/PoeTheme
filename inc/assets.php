<?php
/**
 * Frontend and editor asset registration.
 *
 * Responsibility: enqueue styles/scripts for frontend and editor.
 * It must NOT register theme supports or output inline <head> markup.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

    $global_options = poetheme_get_global_options();

    if ( ! empty( $global_options['enable_media_lightbox'] ) ) {
        wp_enqueue_script( 'poetheme-media-lightbox', POETHEME_URI . '/assets/js/media-lightbox.js', array(), POETHEME_VERSION, true );
        wp_script_add_data( 'poetheme-media-lightbox', 'defer', true );
    }
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
