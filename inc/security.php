<?php
/**
 * Security and sanitization helpers.
 *
 * Responsibility: centralize capability checks and shared sanitizers.
 * It must NOT register options pages or output front-end markup.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check whether the current user can manage theme options.
 *
 * @return bool
 */
function poetheme_user_can_manage_options() {
    return current_user_can( 'manage_options' );
}

/**
 * Sanitize inline CSS values for safe output.
 *
 * @param string $css Raw CSS code.
 * @return string
 */
function poetheme_sanitize_inline_css( $css ) {
    $css = (string) $css;
    $css = wp_kses_no_null( $css );
    $css = wp_strip_all_tags( $css );
    $css = preg_replace( '#/\*.*?\*/#s', '', $css );
    $css = preg_replace( '/@import[^;]+;?/i', '', $css );
    $css = preg_replace( '/@charset[^;]+;?/i', '', $css );
    $css = trim( $css );

    return $css;
}

/**
 * Sanitize custom CSS option values on save.
 *
 * @param string $css Raw CSS input.
 * @return string
 */
function poetheme_sanitize_custom_css( $css ) {
    if ( empty( $css ) ) {
        return '';
    }

    if ( ! poetheme_user_can_manage_options() ) {
        return get_option( 'poetheme_custom_css', '' );
    }

    $css = (string) $css;

    return poetheme_sanitize_inline_css( $css );
}
