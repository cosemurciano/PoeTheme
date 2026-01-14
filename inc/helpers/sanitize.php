<?php
/**
 * Sanitization helpers shared across the theme.
 *
 * Responsibility: sanitize reusable values (no options registration, no UI).
 * It must NOT enqueue assets or print output.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sanitize a single spacing value with units.
 *
 * @param string $value          Raw spacing value.
 * @param bool   $allow_auto     Whether to allow the auto keyword.
 * @param bool   $allow_negative Whether to allow negative values.
 * @return string
 */
function poetheme_sanitize_spacing_value( $value, $allow_auto = false, $allow_negative = false ) {
    $value = trim( (string) $value );

    if ( '' === $value ) {
        return '';
    }

    if ( $allow_auto && 'auto' === strtolower( $value ) ) {
        return 'auto';
    }

    $unit = '';
    if ( preg_match( '/(px|rem|em|%|vh|vw)$/i', $value, $unit_match ) ) {
        $unit         = strtolower( $unit_match[1] );
        $numeric_part = substr( $value, 0, -strlen( $unit_match[1] ) );
    } else {
        $numeric_part = $value;
    }

    $numeric_part = trim( str_replace( ',', '.', $numeric_part ) );

    if ( '' === $numeric_part ) {
        return '';
    }

    if ( ! is_numeric( $numeric_part ) ) {
        return '';
    }

    $number = (float) $numeric_part;

    if ( ! $allow_negative && $number < 0 ) {
        return '';
    }

    if ( 0.0 === $number ) {
        return '0';
    }

    if ( '' === $unit ) {
        $unit = 'px';
    }

    $formatted = poetheme_format_number_for_css( $number );

    if ( '' === $formatted ) {
        $formatted = '0';
    }

    return $formatted . $unit;
}

/**
 * Sanitize a full spacing group (margin/padding).
 *
 * @param mixed $value   Raw spacing group.
 * @param array $default Defaults for the spacing group.
 * @return array
 */
function poetheme_sanitize_spacing_group( $value, $default = array() ) {
    $default_group = poetheme_get_default_spacing_group();

    if ( ! is_array( $default ) || empty( $default ) ) {
        $default = $default_group;
    } else {
        $default = wp_parse_args( $default, $default_group );
        $default['margin']  = wp_parse_args( is_array( $default['margin'] ) ? $default['margin'] : array(), $default_group['margin'] );
        $default['padding'] = wp_parse_args( is_array( $default['padding'] ) ? $default['padding'] : array(), $default_group['padding'] );
    }

    $value  = is_array( $value ) ? $value : array();
    $result = $default;

    $segments = array(
        'margin'  => array(
            'allow_auto'     => true,
            'allow_negative' => true,
        ),
        'padding' => array(
            'allow_auto'     => false,
            'allow_negative' => false,
        ),
    );

    foreach ( $segments as $segment => $rules ) {
        $segment_values = isset( $value[ $segment ] ) && is_array( $value[ $segment ] ) ? $value[ $segment ] : array();

        foreach ( $default[ $segment ] as $side => $default_side_value ) {
            $raw_value = isset( $segment_values[ $side ] ) ? $segment_values[ $side ] : '';
            $result[ $segment ][ $side ] = poetheme_sanitize_spacing_value(
                $raw_value,
                $rules['allow_auto'],
                $rules['allow_negative']
            );
        }
    }

    return $result;
}

/**
 * Determine whether a spacing group contains any usable values.
 *
 * @param mixed $value Potential spacing group.
 * @return bool
 */
function poetheme_is_spacing_group_empty( $value ) {
    if ( ! is_array( $value ) ) {
        return true;
    }

    $segments   = array( 'margin', 'padding' );
    $directions = array( 'top', 'right', 'bottom', 'left' );

    foreach ( $segments as $segment ) {
        if ( empty( $value[ $segment ] ) || ! is_array( $value[ $segment ] ) ) {
            continue;
        }

        foreach ( $directions as $direction ) {
            if ( ! array_key_exists( $direction, $value[ $segment ] ) ) {
                continue;
            }

            $raw = trim( (string) $value[ $segment ][ $direction ] );

            if ( '' !== $raw ) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Convert a spacing group to CSS declarations.
 *
 * @param mixed $value Potential spacing group.
 * @return string
 */
function poetheme_format_spacing_group_for_css( $value ) {
    if ( ! is_array( $value ) ) {
        return '';
    }

    $segments     = array( 'margin', 'padding' );
    $directions   = array( 'top', 'right', 'bottom', 'left' );
    $declarations = array();

    foreach ( $segments as $segment ) {
        if ( empty( $value[ $segment ] ) || ! is_array( $value[ $segment ] ) ) {
            continue;
        }

        foreach ( $directions as $direction ) {
            if ( ! array_key_exists( $direction, $value[ $segment ] ) ) {
                continue;
            }

            $raw = trim( (string) $value[ $segment ][ $direction ] );

            if ( '' === $raw ) {
                continue;
            }

            $declarations[] = $segment . '-' . $direction . ':' . $raw;
        }
    }

    return implode( ';', $declarations );
}

/**
 * Sanitize a font stack string.
 *
 * @param string $value Raw value.
 * @return string
 */
function poetheme_sanitize_font_stack( $value ) {
    $value = wp_strip_all_tags( $value );
    $value = preg_replace( "/[^a-zA-Z0-9,\-\s\"']+/", '', $value );
    return trim( preg_replace( '/\s+/', ' ', $value ) );
}

/**
 * Validate a CSS color string.
 *
 * @param string $color Raw color string.
 * @return bool
 */
function poetheme_is_valid_css_color( $color ) {
    if ( '' === $color ) {
        return true;
    }

    $color = trim( (string) $color );

    if ( '' === $color ) {
        return true;
    }

    if ( 'transparent' === strtolower( $color ) ) {
        return true;
    }

    if ( sanitize_hex_color( $color ) ) {
        return true;
    }

    if ( preg_match( '/^rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/i', $color, $matches ) ) {
        $components = array_slice( $matches, 1 );
        $red        = (int) $components[0];
        $green      = (int) $components[1];
        $blue       = (int) $components[2];

        if ( $red > 255 || $green > 255 || $blue > 255 ) {
            return false;
        }

        if ( isset( $components[3] ) ) {
            $alpha = (float) $components[3];
            if ( $alpha < 0 || $alpha > 1 ) {
                return false;
            }
        }

        return true;
    }

    if ( preg_match( '/^hsla?\(/i', $color ) ) {
        // Basic sanity check for HSLA strings.
        return (bool) preg_match( '/^hsla?\(\s*\d{1,3}(deg|grad|rad|turn)?\s*,\s*\d{1,3}%\s*,\s*\d{1,3}%(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/i', $color );
    }

    return false;
}

/**
 * Normalize CSS color values into a standard format.
 *
 * @param string $color   Raw color string.
 * @param string $default Default fallback.
 * @return string
 */
function poetheme_normalize_color_value( $color, $default = '' ) {
    $color = trim( (string) $color );

    if ( '' === $color ) {
        return '';
    }

    if ( 'transparent' === strtolower( $color ) ) {
        return 'transparent';
    }

    $hex = sanitize_hex_color( $color );
    if ( $hex ) {
        return $hex;
    }

    if ( preg_match( '/^rgba?\(/i', $color ) ) {
        // Normalize spacing.
        if ( poetheme_is_valid_css_color( $color ) ) {
            $color = strtolower( preg_replace( '/\s+/', '', $color ) );
            $color = str_replace( array( 'rgba(', 'rgb(' ), array( 'rgba(', 'rgb(' ), $color );
            return $color;
        }
    }

    if ( preg_match( '/^hsla?\(/i', $color ) && poetheme_is_valid_css_color( $color ) ) {
        $color = strtolower( preg_replace( '/\s+/', '', $color ) );
        return $color;
    }

    return '' === $default ? '' : poetheme_normalize_color_value( $default, '' );
}
