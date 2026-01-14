<?php
/**
 * Utility helpers for formatting values used across the theme.
 *
 * Responsibility: provide small, reusable helpers (no hooks, no output).
 * It must NOT register actions/filters or emit markup.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'poetheme_format_number_for_css' ) ) {
    /**
     * Format a numeric value for CSS output by trimming unnecessary trailing zeros.
     *
     * @param float|int|string $value     Raw numeric value.
     * @param int              $precision Number of decimal places to keep.
     * @return string
     */
    function poetheme_format_number_for_css( $value, $precision = 4 ) {
        $formatted = number_format( (float) $value, (int) $precision, '.', '' );
        $trimmed   = rtrim( rtrim( $formatted, '0' ), '.' );

        return '' === $trimmed ? '0' : $trimmed;
    }
}

if ( ! function_exists( 'poetheme_compile_spacing_css' ) ) {
    /**
     * Convert a spacing option array into CSS declarations.
     *
     * @param array $spacing Spacing values.
     * @return string
     */
    function poetheme_compile_spacing_css( $spacing ) {
        if ( ! is_array( $spacing ) || empty( $spacing ) ) {
            return '';
        }

        $declarations = array();
        $sections     = array(
            'margin'  => array(
                'top'    => 'margin-top',
                'right'  => 'margin-right',
                'bottom' => 'margin-bottom',
                'left'   => 'margin-left',
            ),
            'padding' => array(
                'top'    => 'padding-top',
                'right'  => 'padding-right',
                'bottom' => 'padding-bottom',
                'left'   => 'padding-left',
            ),
        );

        foreach ( $sections as $section => $properties ) {
            if ( empty( $spacing[ $section ] ) || ! is_array( $spacing[ $section ] ) ) {
                continue;
            }

            foreach ( $properties as $side => $property ) {
                if ( ! array_key_exists( $side, $spacing[ $section ] ) ) {
                    continue;
                }

                $value = $spacing[ $section ][ $side ];

                if ( '' === $value ) {
                    continue;
                }

                $declarations[] = $property . ':' . $value;
            }
        }

        if ( empty( $declarations ) ) {
            return '';
        }

        return implode( ';', $declarations ) . ';';
    }
}
