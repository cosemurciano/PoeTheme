<?php
/**
 * Frontend and editor asset registration.
 *
 * Responsibility: enqueue styles/scripts for frontend and editor.
 * It must NOT register theme supports or output inline <head> markup.
 *
 * Asset inventory (M5):
 * Frontend styles:
 * - poetheme-tailwind (CDN, Tailwind CSS 2.2.19)
 * - poetheme-style (style.css)
 *
 * Editor styles:
 * - poetheme-editor-tailwind (CDN, Tailwind CSS 2.2.19)
 * - poetheme-editor-style (assets/css/editor.css)
 *
 * Frontend scripts:
 * - poetheme-navigation (assets/js/navigation.js)
 * - poetheme-alpine (CDN, Alpine.js 3.13.5)
 * - poetheme-lucide (CDN, Lucide 0.294.0)
 * - poetheme-media-lightbox (assets/js/media-lightbox.js, conditional)
 *
 * Editor scripts:
 * - poetheme-editor-alpine (CDN, Alpine.js 3.13.5)
 *
 * Admin scripts (nav menu icons, handled in inc/nav-menu.php):
 * - poetheme-menu-icons (assets/js/menu-icons.js)
 * - poetheme-lucide-admin (CDN, Lucide 0.294.0)
 *
 * Asset policy:
 * - Prefer local assets; CDN is allowed only when pinned, with SRI + crossorigin.
 * - Local assets use filemtime for versioning, falling back to POETHEME_VERSION.
 * - CDN assets use explicit library versions (no "latest").
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CDN asset registry (pinned versions + SRI).
 *
 * @return array
 */
function poetheme_get_cdn_asset_map() {
    return array(
        'poetheme-tailwind' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            'version'    => '2.2.19',
            'integrity'  => 'sha384-HtMZLkYo+pR5/u7zCzXxMJP6QoNnQJt1qkHM0EaOPvGDIzaVZbmYr/TlvUZ/sKAg',
            'crossorigin' => 'anonymous',
        ),
        'poetheme-editor-tailwind' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            'version'    => '2.2.19',
            'integrity'  => 'sha384-HtMZLkYo+pR5/u7zCzXxMJP6QoNnQJt1qkHM0EaOPvGDIzaVZbmYr/TlvUZ/sKAg',
            'crossorigin' => 'anonymous',
        ),
        'poetheme-alpine' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js',
            'version'    => '3.13.5',
            'integrity'  => 'sha384-BxpSbjbDhVKwnC1UfcjsNEuMuxg4af5IXOaSi1Iq5rASQ/9a7uslhEXbP9UI/fXo',
            'crossorigin' => 'anonymous',
        ),
        'poetheme-editor-alpine' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js',
            'version'    => '3.13.5',
            'integrity'  => 'sha384-BxpSbjbDhVKwnC1UfcjsNEuMuxg4af5IXOaSi1Iq5rASQ/9a7uslhEXbP9UI/fXo',
            'crossorigin' => 'anonymous',
        ),
        'poetheme-lucide' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/lucide@0.294.0/dist/umd/lucide.min.js',
            'version'    => '0.294.0',
            'integrity'  => 'sha384-43WP8IQ+5H0ncT+LNM4dZnu+hPINYmeOuNMhTvHfszzXdFjBEji77gkq7TyjQl/U',
            'crossorigin' => 'anonymous',
        ),
        'poetheme-lucide-admin' => array(
            'src'        => 'https://cdn.jsdelivr.net/npm/lucide@0.294.0/dist/umd/lucide.min.js',
            'version'    => '0.294.0',
            'integrity'  => 'sha384-43WP8IQ+5H0ncT+LNM4dZnu+hPINYmeOuNMhTvHfszzXdFjBEji77gkq7TyjQl/U',
            'crossorigin' => 'anonymous',
        ),
    );
}

/**
 * Get CDN asset data for a given handle.
 *
 * @param string $handle Asset handle.
 * @return array
 */
function poetheme_get_cdn_asset( $handle ) {
    $assets = poetheme_get_cdn_asset_map();

    return isset( $assets[ $handle ] ) ? $assets[ $handle ] : array();
}

/**
 * Determine if Alpine.js is needed on the frontend.
 *
 * @return bool
 */
function poetheme_should_load_alpine() {
    $should_load = true;

    return (bool) apply_filters( 'poetheme_should_load_alpine', $should_load );
}

/**
 * Determine if Lucide icons are needed on the frontend.
 *
 * @return bool
 */
function poetheme_should_load_lucide() {
    $should_load = true;

    return (bool) apply_filters( 'poetheme_should_load_lucide', $should_load );
}

/**
 * Determine if the navigation script is needed.
 *
 * @return bool
 */
function poetheme_should_load_navigation() {
    $should_load = has_nav_menu( 'primary' );

    return (bool) apply_filters( 'poetheme_should_load_navigation', $should_load );
}

/**
 * Determine if media lightbox is needed.
 *
 * @return bool
 */
function poetheme_should_load_media_lightbox() {
    $global_options = poetheme_get_global_options();
    $enabled        = ! empty( $global_options['enable_media_lightbox'] );

    if ( ! $enabled ) {
        return false;
    }

    $should_load = is_singular() || is_front_page();

    return (bool) apply_filters( 'poetheme_should_load_media_lightbox', $should_load );
}

/**
 * Determine if Alpine.js is needed in the block editor.
 *
 * @return bool
 */
function poetheme_should_load_editor_alpine() {
    $should_load = false;

    return (bool) apply_filters( 'poetheme_should_load_editor_alpine', $should_load );
}

/**
 * Add integrity + crossorigin attributes to CDN assets.
 *
 * @param string $tag    HTML tag for the enqueued asset.
 * @param string $handle Asset handle.
 * @param string $src    Asset source URL.
 * @return string
 */
function poetheme_add_sri_attributes( $tag, $handle, $src ) {
    $asset = poetheme_get_cdn_asset( $handle );

    if ( empty( $asset['integrity'] ) ) {
        return $tag;
    }

    if ( false !== strpos( $tag, 'integrity=' ) ) {
        return $tag;
    }

    $integrity  = esc_attr( $asset['integrity'] );
    $crossorigin = isset( $asset['crossorigin'] ) ? esc_attr( $asset['crossorigin'] ) : 'anonymous';

    if ( false !== strpos( $tag, '<script' ) ) {
        return str_replace( ' src=', ' integrity="' . $integrity . '" crossorigin="' . $crossorigin . '" src=', $tag );
    }

    if ( false !== strpos( $tag, "rel='stylesheet'" ) || false !== strpos( $tag, 'rel="stylesheet"' ) ) {
        return str_replace( ' rel=', ' integrity="' . $integrity . '" crossorigin="' . $crossorigin . '" rel=', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'poetheme_add_sri_attributes', 10, 3 );
add_filter( 'style_loader_tag', 'poetheme_add_sri_attributes', 10, 3 );

/**
 * Log enqueued assets in debug mode (frontend only).
 *
 * @return void
 */
function poetheme_log_enqueued_assets() {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG || is_admin() ) {
        return;
    }

    global $wp_scripts, $wp_styles;

    if ( empty( $wp_scripts ) || empty( $wp_styles ) ) {
        return;
    }

    $script_entries = array();
    foreach ( $wp_scripts->queue as $handle ) {
        if ( empty( $wp_scripts->registered[ $handle ] ) ) {
            continue;
        }

        $src = $wp_scripts->registered[ $handle ]->src;
        if ( $src && 0 === strpos( $src, '/' ) ) {
            $src = $wp_scripts->base_url . $src;
        }
        $script_entries[] = $handle . ' -> ' . $src;
    }

    $style_entries = array();
    foreach ( $wp_styles->queue as $handle ) {
        if ( empty( $wp_styles->registered[ $handle ] ) ) {
            continue;
        }

        $src = $wp_styles->registered[ $handle ]->src;
        if ( $src && 0 === strpos( $src, '/' ) ) {
            $src = $wp_styles->base_url . $src;
        }
        $style_entries[] = $handle . ' -> ' . $src;
    }

    if ( $style_entries ) {
        error_log( "PoeTheme styles enqueued:\n- " . implode( "\n- ", $style_entries ) );
    }

    if ( $script_entries ) {
        error_log( "PoeTheme scripts enqueued:\n- " . implode( "\n- ", $script_entries ) );
    }
}
add_action( 'wp_print_scripts', 'poetheme_log_enqueued_assets', 99 );

/**
 * Enqueue scripts and styles.
 */
function poetheme_scripts() {
    $tailwind     = poetheme_get_cdn_asset( 'poetheme-tailwind' );
    $tailwind_src = isset( $tailwind['src'] ) ? $tailwind['src'] : 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';
    $tailwind_ver = isset( $tailwind['version'] ) ? $tailwind['version'] : POETHEME_VERSION;
    wp_enqueue_style( 'poetheme-tailwind', $tailwind_src, array(), $tailwind_ver );
    wp_enqueue_style( 'poetheme-style', get_stylesheet_uri(), array( 'poetheme-tailwind' ), poetheme_get_asset_version( 'style.css' ) );

    if ( poetheme_should_load_alpine() ) {
        $alpine     = poetheme_get_cdn_asset( 'poetheme-alpine' );
        $alpine_src = isset( $alpine['src'] ) ? $alpine['src'] : 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js';
        $alpine_ver = isset( $alpine['version'] ) ? $alpine['version'] : POETHEME_VERSION;
        wp_enqueue_script( 'poetheme-alpine', $alpine_src, array(), $alpine_ver, true );
        wp_script_add_data( 'poetheme-alpine', 'defer', true );
    }

    if ( poetheme_should_load_lucide() ) {
        $lucide     = poetheme_get_cdn_asset( 'poetheme-lucide' );
        $lucide_src = isset( $lucide['src'] ) ? $lucide['src'] : 'https://cdn.jsdelivr.net/npm/lucide@0.294.0/dist/umd/lucide.min.js';
        $lucide_ver = isset( $lucide['version'] ) ? $lucide['version'] : POETHEME_VERSION;
        wp_enqueue_script( 'poetheme-lucide', $lucide_src, array(), $lucide_ver, true );
        wp_script_add_data( 'poetheme-lucide', 'defer', true );

        wp_add_inline_script(
            'poetheme-lucide',
            'document.addEventListener("DOMContentLoaded",function(){if(window.lucide){window.lucide.createIcons();}});'
        );
    }

    if ( poetheme_should_load_navigation() ) {
        wp_enqueue_script( 'poetheme-navigation', POETHEME_URI . '/assets/js/navigation.js', array(), poetheme_get_asset_version( 'assets/js/navigation.js' ), true );
        wp_script_add_data( 'poetheme-navigation', 'defer', true );
    }

    if ( poetheme_should_load_media_lightbox() ) {
        wp_enqueue_script( 'poetheme-media-lightbox', POETHEME_URI . '/assets/js/media-lightbox.js', array(), poetheme_get_asset_version( 'assets/js/media-lightbox.js' ), true );
        wp_script_add_data( 'poetheme-media-lightbox', 'defer', true );
    }
}
add_action( 'wp_enqueue_scripts', 'poetheme_scripts' );

/**
 * Block editor assets.
 */
function poetheme_block_editor_assets() {
    $tailwind     = poetheme_get_cdn_asset( 'poetheme-editor-tailwind' );
    $tailwind_src = isset( $tailwind['src'] ) ? $tailwind['src'] : 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';
    $tailwind_ver = isset( $tailwind['version'] ) ? $tailwind['version'] : POETHEME_VERSION;
    wp_enqueue_style( 'poetheme-editor-tailwind', $tailwind_src, array(), $tailwind_ver );
    wp_enqueue_style( 'poetheme-editor-style', POETHEME_URI . '/assets/css/editor.css', array( 'poetheme-editor-tailwind' ), poetheme_get_asset_version( 'assets/css/editor.css' ) );

    if ( poetheme_should_load_editor_alpine() ) {
        $alpine     = poetheme_get_cdn_asset( 'poetheme-editor-alpine' );
        $alpine_src = isset( $alpine['src'] ) ? $alpine['src'] : 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js';
        $alpine_ver = isset( $alpine['version'] ) ? $alpine['version'] : POETHEME_VERSION;
        wp_enqueue_script( 'poetheme-editor-alpine', $alpine_src, array(), $alpine_ver, true );
        wp_script_add_data( 'poetheme-editor-alpine', 'defer', true );
    }

    $font_styles = poetheme_prepare_font_styles();

    if ( ! empty( $font_styles['font_faces'] ) || ! empty( $font_styles['css_rules'] ) ) {
        wp_add_inline_style( 'poetheme-editor-style', $font_styles['font_faces'] . $font_styles['css_rules'] );
    }
}
add_action( 'enqueue_block_editor_assets', 'poetheme_block_editor_assets' );

/**
 * Admin assets for header layout selection.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function poetheme_admin_header_layout_assets( $hook ) {
    if ( 'poetheme_page_poetheme-header' !== $hook ) {
        return;
    }

    wp_enqueue_style(
        'poetheme-admin-header-layouts',
        POETHEME_URI . '/assets/css/admin-header-layouts.css',
        array(),
        poetheme_get_asset_version( 'assets/css/admin-header-layouts.css' )
    );
}
add_action( 'admin_enqueue_scripts', 'poetheme_admin_header_layout_assets' );
