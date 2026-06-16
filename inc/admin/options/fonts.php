<?php
/**
 * Custom font discovery, options, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function poetheme_get_font_directory() {
    return apply_filters( 'poetheme_font_directory', trailingslashit( get_template_directory() ) . 'theme-font' );
}

/**
 * Retrieve the URI pointing to the custom theme fonts directory.
 *
 * @return string
 */

function poetheme_get_font_directory_uri() {
    return apply_filters( 'poetheme_font_directory_uri', trailingslashit( get_template_directory_uri() ) . 'theme-font' );
}

/**
 * Map a font file extension to a CSS format value.
 *
 * @param string $extension File extension.
 * @return string
 */

function poetheme_map_font_format( $extension ) {
    switch ( strtolower( $extension ) ) {
        case 'woff2':
            return 'woff2';
        case 'woff':
            return 'woff';
        case 'ttf':
            return 'truetype';
        case 'otf':
        default:
            return 'opentype';
    }
}

/**
 * Attempt to detect the font weight from a filename.
 *
 * @param string $filename Font filename without extension.
 * @return int
 */

function poetheme_detect_font_weight( $filename ) {
    $lookup = array(
        'thin'       => 100,
        'extralight' => 200,
        'ultralight' => 200,
        'light'      => 300,
        'regular'    => 400,
        'book'       => 400,
        'medium'     => 500,
        'semibold'   => 600,
        'demibold'   => 600,
        'bold'       => 700,
        'extrabold'  => 800,
        'ultrabold'  => 800,
        'black'      => 900,
        'heavy'      => 900,
    );

    $filename = strtolower( $filename );

    foreach ( $lookup as $keyword => $weight ) {
        if ( false !== strpos( $filename, $keyword ) ) {
            return $weight;
        }
    }

    if ( preg_match( '/(\d{3})/', $filename, $matches ) ) {
        $weight = (int) $matches[1];
        return min( 900, max( 100, $weight ) );
    }

    return 400;
}

/**
 * Detect the font style from a filename.
 *
 * @param string $filename Font filename without extension.
 * @return string
 */

function poetheme_detect_font_style( $filename ) {
    $filename = strtolower( $filename );

    if ( false !== strpos( $filename, 'italic' ) || false !== strpos( $filename, 'oblique' ) ) {
        return 'italic';
    }

    return 'normal';
}

/**
 * Retrieve a sanitized list of custom fonts stored in the theme-font directory.
 *
 * @return array
 */

function poetheme_get_available_fonts() {
    static $fonts = null;

    if ( null !== $fonts ) {
        return $fonts;
    }

    $fonts     = array();
    $directory = poetheme_get_font_directory();

    if ( ! is_dir( $directory ) ) {
        return $fonts;
    }

    $pattern = trailingslashit( $directory ) . '*.{woff2,woff,ttf,otf}';
    $files   = glob( $pattern, GLOB_BRACE );

    if ( ! $files ) {
        return $fonts;
    }

    foreach ( $files as $file ) {
        $pathinfo = pathinfo( $file );

        if ( empty( $pathinfo['filename'] ) || empty( $pathinfo['extension'] ) ) {
            continue;
        }

        $slug = sanitize_title( $pathinfo['filename'] );

        if ( isset( $fonts[ $slug ] ) ) {
            continue;
        }

        $family = preg_replace( '/[^a-zA-Z0-9\-\s]/', ' ', $pathinfo['filename'] );
        $family = trim( preg_replace( '/[\s_-]+/', ' ', $family ) );
        $family = $family ? ucwords( $family ) : 'Font ' . strtoupper( $slug );

        $fonts[ $slug ] = array(
            'slug'   => $slug,
            'family' => $family,
            'file'   => $pathinfo['basename'],
            'format' => poetheme_map_font_format( $pathinfo['extension'] ),
            'style'  => poetheme_detect_font_style( $pathinfo['filename'] ),
            'weight' => poetheme_detect_font_weight( $pathinfo['filename'] ),
            'url'    => esc_url_raw( trailingslashit( poetheme_get_font_directory_uri() ) . rawurlencode( $pathinfo['basename'] ) ),
        );
    }

    ksort( $fonts );

    return $fonts;
}

/**
 * Generate @font-face rules for the provided font slugs.
 *
 * @param array|null $font_slugs Font slugs to include. Null includes all available fonts.
 * @return string
 */

function poetheme_generate_font_face_css( $font_slugs = null ) {
    $available_fonts = poetheme_get_available_fonts();

    if ( empty( $available_fonts ) ) {
        return '';
    }

    if ( null === $font_slugs ) {
        $selected_fonts = $available_fonts;
    } else {
        $selected_fonts = array();
        foreach ( (array) $font_slugs as $slug ) {
            $slug = sanitize_title( $slug );
            if ( isset( $available_fonts[ $slug ] ) ) {
                $selected_fonts[ $slug ] = $available_fonts[ $slug ];
            }
        }
    }

    if ( empty( $selected_fonts ) ) {
        return '';
    }

    $rules = '';

    foreach ( $selected_fonts as $font ) {
        $family = preg_replace( '/[^a-zA-Z0-9\s\-]/', '', $font['family'] );
        $style  = in_array( $font['style'], array( 'normal', 'italic' ), true ) ? $font['style'] : 'normal';
        $weight = (int) $font['weight'];
        $weight = $weight ? $weight : 400;
        $url    = esc_url_raw( $font['url'] );
        $format = $font['format'];

        $rules .= "@font-face{font-family:'{$family}';font-style:{$style};font-weight:{$weight};font-display:swap;src:url('{$url}') format('{$format}');}";
    }

    return $rules;
}

/**
 * Default font options.
 *
 * @return array
 */

function poetheme_get_default_font_options() {
    $defaults = array(
        'body_font'        => '',
        'body_fallback'    => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
        'heading_font'     => '',
        'heading_fallback' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
    );

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['option_key'] ) ) {
            continue;
        }

        $key = $field['option_key'];

        if ( ! isset( $defaults[ $key ] ) ) {
            $defaults[ $key ] = '';
        }

        if ( ! empty( $field['size'] ) && ! empty( $field['size']['option_key'] ) ) {
            $size_key = $field['size']['option_key'];

            if ( ! isset( $defaults[ $size_key ] ) ) {
                $defaults[ $size_key ] = isset( $field['size']['default'] ) ? $field['size']['default'] : '';
            }
        }

        if ( ! empty( $field['border_radius'] ) && ! empty( $field['border_radius']['option_key'] ) ) {
            $radius_key = $field['border_radius']['option_key'];

            if ( ! isset( $defaults[ $radius_key ] ) ) {
                $defaults[ $radius_key ] = isset( $field['border_radius']['default'] ) ? $field['border_radius']['default'] : '';
            }
        }

        if ( ! empty( $field['spacing'] ) && ! empty( $field['spacing']['option_key'] ) ) {
            $spacing_key = $field['spacing']['option_key'];

            if ( ! isset( $defaults[ $spacing_key ] ) ) {
                $defaults[ $spacing_key ] = poetheme_get_default_spacing_group();
            }
        }
    }

    return $defaults;
}

/**
 * Retrieve saved font options merged with defaults.
 *
 * @return array
 */

function poetheme_get_font_options() {
    $options = get_option( 'poetheme_fonts', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $defaults = poetheme_get_default_font_options();
    $options  = wp_parse_args( $options, $defaults );

    $spacing_keys = array();

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['spacing'] ) || empty( $field['spacing']['option_key'] ) ) {
            continue;
        }

        $spacing_keys[] = $field['spacing']['option_key'];
    }

    if ( $spacing_keys ) {
        $spacing_keys   = array_values( array_unique( $spacing_keys ) );
        $legacy_colors  = get_option( 'poetheme_colors', array() );
        $legacy_colors  = is_array( $legacy_colors ) ? $legacy_colors : array();

        foreach ( $spacing_keys as $spacing_key ) {
            if ( ! array_key_exists( $spacing_key, $defaults ) ) {
                continue;
            }

            $current_value = isset( $options[ $spacing_key ] ) ? $options[ $spacing_key ] : array();

            if ( ! poetheme_is_spacing_group_empty( $current_value ) ) {
                continue;
            }

            if ( isset( $legacy_colors[ $spacing_key ] ) && is_array( $legacy_colors[ $spacing_key ] ) ) {
                $options[ $spacing_key ] = poetheme_sanitize_spacing_group( $legacy_colors[ $spacing_key ], $defaults[ $spacing_key ] );
            }
        }
    }

    return $options;
}

/**
 * Sanitize font options before saving.
 *
 * @param array $input Raw input.
 * @return array
 */

function poetheme_sanitize_font_options( $input ) {
    $input   = is_array( $input ) ? $input : array();
    $fonts   = poetheme_get_available_fonts();
    $default = poetheme_get_default_font_options();
    $output  = poetheme_get_font_options();

    if ( ! poetheme_user_can_manage_options() ) {
        return $output;
    }

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['option_key'] ) ) {
            continue;
        }

        $key   = $field['option_key'];
        $value = isset( $input[ $key ] ) ? sanitize_title( $input[ $key ] ) : '';

        if ( $value && ! isset( $fonts[ $value ] ) ) {
            $value = '';
        }

        $output[ $key ] = $value;
    }

    $size_fields = array();

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['size'] ) || empty( $field['size']['option_key'] ) ) {
            continue;
        }

        $size_key = $field['size']['option_key'];

        if ( isset( $size_fields[ $size_key ] ) ) {
            continue;
        }

        $size_fields[ $size_key ] = $field['size'];
    }

    foreach ( $size_fields as $size_key => $size_config ) {
        $raw_value = isset( $input[ $size_key ] ) ? trim( (string) $input[ $size_key ] ) : '';

        if ( '' === $raw_value ) {
            $default_value       = isset( $size_config['default'] ) ? $size_config['default'] : ( isset( $default[ $size_key ] ) ? $default[ $size_key ] : '' );
            $output[ $size_key ] = $default_value;
            continue;
        }

        $normalized = str_replace( ',', '.', $raw_value );

        if ( ! is_numeric( $normalized ) ) {
            $output[ $size_key ] = '';
            continue;
        }

        $value = (float) $normalized;

        if ( isset( $size_config['min'] ) ) {
            $value = max( (float) $size_config['min'], $value );
        }

        if ( isset( $size_config['max'] ) ) {
            $value = min( (float) $size_config['max'], $value );
        }

        $output[ $size_key ] = $value > 0 ? $value : '';
    }

    $radius_fields = array();

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['border_radius'] ) || empty( $field['border_radius']['option_key'] ) ) {
            continue;
        }

        $radius_key = $field['border_radius']['option_key'];

        if ( isset( $radius_fields[ $radius_key ] ) ) {
            continue;
        }

        $radius_fields[ $radius_key ] = $field['border_radius'];
    }

    foreach ( $radius_fields as $radius_key => $radius_config ) {
        $raw_value = isset( $input[ $radius_key ] ) ? trim( (string) $input[ $radius_key ] ) : '';

        if ( '' === $raw_value ) {
            $default_value        = isset( $radius_config['default'] ) ? $radius_config['default'] : ( isset( $default[ $radius_key ] ) ? $default[ $radius_key ] : '' );
            $output[ $radius_key ] = $default_value;
            continue;
        }

        $normalized = str_replace( ',', '.', $raw_value );

        if ( ! is_numeric( $normalized ) ) {
            $output[ $radius_key ] = '';
            continue;
        }

        $value = (float) $normalized;

        if ( isset( $radius_config['min'] ) ) {
            $value = max( (float) $radius_config['min'], $value );
        }

        if ( isset( $radius_config['max'] ) ) {
            $value = min( (float) $radius_config['max'], $value );
        }

        if ( $value < 0 ) {
            $value = 0;
        }

        $output[ $radius_key ] = $value;
    }

    $spacing_fields = array();

    foreach ( poetheme_get_font_field_config() as $field ) {
        if ( empty( $field['spacing'] ) || empty( $field['spacing']['option_key'] ) ) {
            continue;
        }

        $spacing_key = $field['spacing']['option_key'];

        if ( isset( $spacing_fields[ $spacing_key ] ) ) {
            continue;
        }

        $spacing_fields[ $spacing_key ] = $field['spacing'];
    }

    foreach ( $spacing_fields as $spacing_key => $spacing_config ) {
        $raw_value = isset( $input[ $spacing_key ] ) ? $input[ $spacing_key ] : array();
        $default_value = isset( $default[ $spacing_key ] ) && is_array( $default[ $spacing_key ] ) ? $default[ $spacing_key ] : poetheme_get_default_spacing_group();

        $output[ $spacing_key ] = poetheme_sanitize_spacing_group( $raw_value, $default_value );
    }

    $body_fallback    = isset( $input['body_fallback'] ) ? poetheme_sanitize_font_stack( $input['body_fallback'] ) : $default['body_fallback'];
    $heading_fallback = isset( $input['heading_fallback'] ) ? poetheme_sanitize_font_stack( $input['heading_fallback'] ) : $default['heading_fallback'];

    $output['body_fallback']    = $body_fallback ? $body_fallback : $default['body_fallback'];
    $output['heading_fallback'] = $heading_fallback ? $heading_fallback : $default['heading_fallback'];

    return $output;
}

/**
 * Build a font-family stack combining the custom font and fallback values.
 *
 * @param string $font_slug Selected font slug.
 * @param string $fallback  Fallback stack.
 * @param array  $available_fonts Optional cached fonts list.
 * @return string
 */

function poetheme_build_font_stack( $font_slug, $fallback, $available_fonts = null ) {
    if ( null === $available_fonts ) {
        $available_fonts = poetheme_get_available_fonts();
    }

    $families = array();
    $font_slug = $font_slug ? sanitize_title( $font_slug ) : '';

    if ( $font_slug && isset( $available_fonts[ $font_slug ] ) ) {
        $family_name = preg_replace( '/[^a-zA-Z0-9\s\-]/', '', $available_fonts[ $font_slug ]['family'] );
        if ( $family_name ) {
            $families[] = "'{$family_name}'";
        }
    }

    if ( $fallback ) {
        $fallback_parts = array_map( 'trim', explode( ',', $fallback ) );
        foreach ( $fallback_parts as $part ) {
            if ( '' === $part ) {
                continue;
            }

            if ( preg_match( '/^(["\']).*\1$/', $part ) ) {
                $families[] = $part;
            } elseif ( false !== strpos( $part, ' ' ) ) {
                $families[] = "'{$part}'";
            } else {
                $families[] = $part;
            }
        }
    }

    $families = array_values( array_unique( array_filter( $families ) ) );

    return implode( ', ', $families );
}

/**
 * Prepare font styles (font-face rules and CSS stacks).
 *
 * @return array
 */

function poetheme_prepare_font_styles() {
    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $options         = poetheme_get_font_options();
    $available_fonts = poetheme_get_available_fonts();

    $config = poetheme_get_font_field_config();

    $global_options      = poetheme_get_global_options();
    $global_font_slug    = isset( $global_options['default_font'] ) ? $global_options['default_font'] : '';
    $global_font_fallback = isset( $global_options['default_font_fallback'] ) ? $global_options['default_font_fallback'] : '';
    $global_font_stack   = '';

    if ( $global_font_slug || $global_font_fallback ) {
        $global_font_stack = poetheme_build_font_stack( $global_font_slug, $global_font_fallback, $available_fonts );
    }

    $selected_fonts = array();
    if ( $global_font_slug ) {
        $selected_fonts[] = $global_font_slug;
    }

    $css_rules      = '';
    $body_stack     = '';
    $heading_stack  = '';

    foreach ( $config as $field ) {
        if ( empty( $field['option_key'] ) ) {
            continue;
        }

        $key           = $field['option_key'];
        $value         = isset( $options[ $key ] ) ? $options[ $key ] : '';
        $fallback_key  = ! empty( $field['fallback_key'] ) ? $field['fallback_key'] : '';
        $fallback_value = $fallback_key && isset( $options[ $fallback_key ] ) ? $options[ $fallback_key ] : '';

        if ( $global_font_slug && '' === $value && in_array( $key, array( 'body_font', 'heading_font' ), true ) ) {
            $value = $global_font_slug;
        }

        if ( $global_font_fallback && '' === $fallback_value ) {
            $fallback_value = $global_font_fallback;
        }

        if ( $value ) {
            $selected_fonts[] = $value;
        }

        if ( empty( $value ) && empty( $fallback_value ) ) {
            continue;
        }

        $stack = poetheme_build_font_stack( $value, $fallback_value, $available_fonts );

        if ( ! $stack ) {
            continue;
        }

        if ( 'body_font' === $key ) {
            $body_stack = $stack;
        }

        if ( 'heading_font' === $key ) {
            $heading_stack = $stack;
        }

        if ( ! empty( $field['selectors'] ) ) {
            $selectors = array_filter( array_map( 'trim', (array) $field['selectors'] ) );

            if ( $selectors ) {
                $css_rules .= implode( ',', $selectors ) . '{font-family:' . $stack . ';}';
            }
        }
    }

    $size_rules = '';

    foreach ( $config as $field ) {
        if ( empty( $field['size'] ) || empty( $field['size']['option_key'] ) ) {
            continue;
        }

        $size_key = $field['size']['option_key'];
        $size_val = isset( $options[ $size_key ] ) ? $options[ $size_key ] : '';

        if ( '' === $size_val || ! is_numeric( $size_val ) ) {
            continue;
        }

        $size_selectors = array();

        if ( ! empty( $field['size']['selectors'] ) ) {
            $size_selectors = (array) $field['size']['selectors'];
        } elseif ( ! empty( $field['selectors'] ) ) {
            $size_selectors = (array) $field['selectors'];
        }

        $size_selectors = array_filter( array_map( 'trim', $size_selectors ) );

        if ( empty( $size_selectors ) ) {
            continue;
        }

        $size_rules .= implode( ',', $size_selectors ) . '{font-size:' . poetheme_format_number_for_css( $size_val ) . 'rem;}';
    }

    $radius_rules = '';

    foreach ( $config as $field ) {
        if ( empty( $field['border_radius'] ) || empty( $field['border_radius']['option_key'] ) ) {
            continue;
        }

        $radius_key = $field['border_radius']['option_key'];
        $radius_val = isset( $options[ $radius_key ] ) ? $options[ $radius_key ] : '';

        if ( '' === $radius_val && '0' !== $radius_val ) {
            continue;
        }

        if ( ! is_numeric( $radius_val ) ) {
            continue;
        }

        $radius_selectors = array();

        if ( ! empty( $field['border_radius']['selectors'] ) ) {
            $radius_selectors = (array) $field['border_radius']['selectors'];
        } elseif ( ! empty( $field['selectors'] ) ) {
            $radius_selectors = (array) $field['selectors'];
        }

        $radius_selectors = array_filter( array_map( 'trim', $radius_selectors ) );

        if ( empty( $radius_selectors ) ) {
            continue;
        }

        $unit = ! empty( $field['border_radius']['unit'] ) ? $field['border_radius']['unit'] : 'px';

        $radius_rules .= implode( ',', $radius_selectors ) . '{border-radius:' . poetheme_format_number_for_css( $radius_val ) . $unit . ';}';
    }

    $spacing_rules = '';

    foreach ( $config as $field ) {
        if ( empty( $field['spacing'] ) || empty( $field['spacing']['option_key'] ) ) {
            continue;
        }

        $spacing_key = $field['spacing']['option_key'];
        $spacing_val = isset( $options[ $spacing_key ] ) ? $options[ $spacing_key ] : array();

        if ( poetheme_is_spacing_group_empty( $spacing_val ) ) {
            continue;
        }

        $spacing_selectors = array();

        if ( ! empty( $field['spacing']['selectors'] ) ) {
            $spacing_selectors = (array) $field['spacing']['selectors'];
        } elseif ( ! empty( $field['selectors'] ) ) {
            $spacing_selectors = (array) $field['selectors'];
        }

        $spacing_selectors = array_filter( array_map( 'trim', $spacing_selectors ) );

        if ( empty( $spacing_selectors ) ) {
            continue;
        }

        $declarations = poetheme_format_spacing_group_for_css( $spacing_val );

        if ( '' === $declarations ) {
            continue;
        }

        $spacing_rules .= implode( ',', $spacing_selectors ) . '{' . $declarations . ';}';
    }

    $global_rules = '';

    if ( $global_font_stack && ! $body_stack ) {
        $body_stack   = $global_font_stack;
        $global_rules = 'body,button,input,select,textarea{font-family:' . $global_font_stack . ';}';
    }

    if ( $global_rules ) {
        $css_rules = $global_rules . $css_rules;
    }

    $selected_fonts = array_values( array_unique( array_filter( $selected_fonts ) ) );

    $font_faces = poetheme_generate_font_face_css( $selected_fonts );

    if ( $size_rules ) {
        $css_rules .= $size_rules;
    }

    if ( $radius_rules ) {
        $css_rules .= $radius_rules;
    }

    if ( $spacing_rules ) {
        $css_rules .= $spacing_rules;
    }

    $cache = array(
        'font_faces'    => $font_faces,
        'css_rules'     => $css_rules,
        'body_stack'    => $body_stack,
        'heading_stack' => $heading_stack,
        'used_fonts'    => $selected_fonts,
    );

    return $cache;
}

/**
 * Sanitize global layout options.
 *
 * @param array $input Raw option values.
 * @return array
 */

function poetheme_get_font_field_config() {
    return array(
        'content_text_color' => array(
            'option_key'      => 'body_font',
            'label'           => __( 'Font testo del contenuto', 'poetheme' ),
            'description'     => __( 'Scegli il font principale per paragrafi e testi di base.', 'poetheme' ),
            'default_label'   => __( 'Usa il font di sistema predefinito', 'poetheme' ),
            'fallback_key'    => 'body_fallback',
            'fallback'        => array(
                'key'         => 'body_fallback',
                'label'       => __( 'Font di fallback', 'poetheme' ),
                'description' => __( 'Elenca i font alternativi da usare se il font principale non è disponibile (esempio: "Arial, Helvetica, sans-serif").', 'poetheme' ),
            ),
            'preview_variant' => 'text',
            'sample'          => __( 'Questo è un esempio di paragrafo con il font selezionato.', 'poetheme' ),
            'selectors'       => array(
                'body',
                'button',
                'input',
                'select',
                'textarea',
            ),
            'size'            => array(
                'option_key'  => 'body_font_size',
                'label'       => __( 'Dimensione testo (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione base del font per il contenuto usando i rem.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 3,
                'step'        => 0.05,
                'default'     => 1,
            ),
        ),
        'cta_text_color' => array(
            'option_key'      => 'cta_text_font',
            'label'           => __( 'Font testo Call to Action', 'poetheme' ),
            'description'     => __( 'Font del testo all’interno del pulsante di invito all’azione.', 'poetheme' ),
            'default_label'   => __( 'Usa il font del testo principale', 'poetheme' ),
            'fallback_key'    => 'body_fallback',
            'preview_variant' => 'button',
            'sample'          => __( 'Call to action di esempio', 'poetheme' ),
            'selectors'       => array(
                '.poetheme-cta-button',
            ),
            'size'            => array(
                'option_key'  => 'cta_text_font_size',
                'label'       => __( 'Dimensione pulsante (rem)', 'poetheme' ),
                'description' => __( 'Definisce la dimensione del testo del pulsante Call to Action in rem.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 3,
                'step'        => 0.05,
            ),
            'border_radius'   => array(
                'option_key'  => 'cta_button_border_radius',
                'label'       => __( 'Raggio angoli pulsante (px)', 'poetheme' ),
                'description' => __( 'Imposta un raggio uniforme per tutti gli angoli del pulsante Call to Action.', 'poetheme' ),
                'min'         => 0,
                'max'         => 200,
                'step'        => 1,
                'unit'        => 'px',
                'default'     => '',
            ),
        ),
        'footer_widget_heading_color' => array(
            'option_key'      => 'footer_widget_heading_font',
            'label'           => __( 'Font titoli widget footer', 'poetheme' ),
            'description'     => __( 'Scegli il font per i titoli (H) dei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo widget di esempio', 'poetheme' ),
            'selectors'       => array(
                '.poetheme-footer-widgets h2',
                '.poetheme-footer-widgets h3',
                '.poetheme-footer-widgets h4',
                '.poetheme-footer-widgets h5',
            ),
            'size'            => array(
                'option_key'  => 'footer_widget_heading_font_size',
                'label'       => __( 'Dimensione titoli widget (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione delle intestazioni dei widget del footer in rem.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'column'          => 'left',
        ),
        'footer_widget_heading_h2_font' => array(
            'option_key'      => 'footer_widget_heading_h2_font',
            'label'           => __( 'Font titolo widget H2', 'poetheme' ),
            'description'     => __( 'Applica un font specifico ai titoli H2 presenti nei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli widget', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo widget H2', 'poetheme' ),
            'selectors'       => array( '.poetheme-footer-widgets h2' ),
            'size'            => array(
                'option_key'  => 'footer_widget_heading_h2_font_size',
                'label'       => __( 'Dimensione H2 (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione dei titoli H2 nei widget del piè di pagina.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 4,
                'step'        => 0.05,
                'selectors'   => array( '.poetheme-footer-widgets h2' ),
            ),
            'column'          => 'right',
            'spacing'         => array(
                'option_key'  => 'footer_widget_heading_h2_spacing',
                'label'       => __( 'Margini e padding H2 widget (px)', 'poetheme' ),
                'description' => __( 'Imposta valori in px, come 20px, per i margini e il padding dei titoli H2 nelle aree widget del piè di pagina.', 'poetheme' ),
                'selectors'   => array( '.poetheme-footer-widgets h2' ),
            ),
        ),
        'footer_widget_heading_h3_font' => array(
            'option_key'      => 'footer_widget_heading_h3_font',
            'label'           => __( 'Font titolo widget H3', 'poetheme' ),
            'description'     => __( 'Applica un font specifico ai titoli H3 dei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli widget', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo widget H3', 'poetheme' ),
            'selectors'       => array( '.poetheme-footer-widgets h3' ),
            'size'            => array(
                'option_key'  => 'footer_widget_heading_h3_font_size',
                'label'       => __( 'Dimensione H3 (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione dei titoli H3 nei widget del piè di pagina.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 4,
                'step'        => 0.05,
                'selectors'   => array( '.poetheme-footer-widgets h3' ),
            ),
            'column'          => 'right',
            'spacing'         => array(
                'option_key'  => 'footer_widget_heading_h3_spacing',
                'label'       => __( 'Margini e padding H3 widget (px)', 'poetheme' ),
                'description' => __( 'Utilizza valori in px, ad esempio 18px, per i margini e il padding dei titoli H3 nelle aree widget del piè di pagina.', 'poetheme' ),
                'selectors'   => array( '.poetheme-footer-widgets h3' ),
            ),
        ),
        'footer_widget_heading_h4_font' => array(
            'option_key'      => 'footer_widget_heading_h4_font',
            'label'           => __( 'Font titolo widget H4', 'poetheme' ),
            'description'     => __( 'Applica un font specifico ai titoli H4 presenti nei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli widget', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo widget H4', 'poetheme' ),
            'selectors'       => array( '.poetheme-footer-widgets h4' ),
            'size'            => array(
                'option_key'  => 'footer_widget_heading_h4_font_size',
                'label'       => __( 'Dimensione H4 (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione dei titoli H4 nei widget del piè di pagina.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 4,
                'step'        => 0.05,
                'selectors'   => array( '.poetheme-footer-widgets h4' ),
            ),
            'column'          => 'right',
            'spacing'         => array(
                'option_key'  => 'footer_widget_heading_h4_spacing',
                'label'       => __( 'Margini e padding H4 widget (px)', 'poetheme' ),
                'description' => __( 'Specifica valori in px, per esempio 16px, per margini e padding dei titoli H4 nelle aree widget del piè di pagina.', 'poetheme' ),
                'selectors'   => array( '.poetheme-footer-widgets h4' ),
            ),
        ),
        'footer_widget_heading_h5_font' => array(
            'option_key'      => 'footer_widget_heading_h5_font',
            'label'           => __( 'Font titolo widget H5', 'poetheme' ),
            'description'     => __( 'Applica un font specifico ai titoli H5 presenti nei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli widget', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo widget H5', 'poetheme' ),
            'selectors'       => array( '.poetheme-footer-widgets h5' ),
            'size'            => array(
                'option_key'  => 'footer_widget_heading_h5_font_size',
                'label'       => __( 'Dimensione H5 (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione dei titoli H5 nei widget del piè di pagina.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 4,
                'step'        => 0.05,
                'selectors'   => array( '.poetheme-footer-widgets h5' ),
            ),
            'column'          => 'right',
            'spacing'         => array(
                'option_key'  => 'footer_widget_heading_h5_spacing',
                'label'       => __( 'Margini e padding H5 widget (px)', 'poetheme' ),
                'description' => __( 'Immetti valori in px, come 14px, per i margini e il padding dei titoli H5 nelle aree widget del piè di pagina.', 'poetheme' ),
                'selectors'   => array( '.poetheme-footer-widgets h5' ),
            ),
        ),
        'footer_widget_text_color' => array(
            'option_key'      => 'footer_widget_text_font',
            'label'           => __( 'Font testo widget footer', 'poetheme' ),
            'description'     => __( 'Definisci il font del testo dei widget del piè di pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa il font del testo principale', 'poetheme' ),
            'fallback_key'    => 'body_fallback',
            'preview_variant' => 'text',
            'sample'          => __( 'Testo del widget footer di esempio.', 'poetheme' ),
            'selectors'       => array(
                '.poetheme-footer-widgets',
                '.poetheme-footer-widgets p',
                '.poetheme-footer-widgets li',
                '.poetheme-footer-widgets a',
                '.poetheme-footer-widgets span',
            ),
            'size'            => array(
                'option_key'  => 'footer_widget_text_font_size',
                'label'       => __( 'Dimensione testo widget (rem)', 'poetheme' ),
                'description' => __( 'Regola la dimensione del testo all’interno dei widget del footer in rem.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 3,
                'step'        => 0.05,
                'selectors'   => array( '.poetheme-footer-widgets' ),
            ),
        ),
        'top_bar_text_color' => array(
            'option_key'      => 'top_bar_text_font',
            'label'           => __( 'Font testo barra superiore', 'poetheme' ),
            'description'     => __( 'Font delle informazioni mostrate nella barra superiore.', 'poetheme' ),
            'default_label'   => __( 'Usa il font del testo principale', 'poetheme' ),
            'fallback_key'    => 'body_fallback',
            'preview_variant' => 'text',
            'sample'          => __( 'Testo della barra superiore di esempio.', 'poetheme' ),
            'selectors'       => array(
                '.poetheme-top-bar',
                '.poetheme-top-bar p',
                '.poetheme-top-bar span',
                '.poetheme-top-bar a',
            ),
            'size'            => array(
                'option_key'  => 'top_bar_text_font_size',
                'label'       => __( 'Dimensione barra superiore (rem)', 'poetheme' ),
                'description' => __( 'Regola la dimensione del testo mostrato nella barra superiore in rem.', 'poetheme' ),
                'min'         => 0.5,
                'max'         => 2,
                'step'        => 0.05,
            ),
        ),
        'heading_h1_color' => array(
            'option_key'      => 'heading_font',
            'label'           => __( 'Font titolo H1', 'poetheme' ),
            'description'     => __( 'Font del titolo principale della pagina.', 'poetheme' ),
            'default_label'   => __( 'Usa lo stesso font del testo', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'fallback'        => array(
                'key'         => 'heading_fallback',
                'label'       => __( 'Font di fallback titoli', 'poetheme' ),
                'description' => __( 'Specifica i font alternativi per titoli e intestazioni.', 'poetheme' ),
            ),
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H1', 'poetheme' ),
            'selectors'       => array(
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
            ),
            'size'            => array(
                'option_key'  => 'heading_font_size',
                'label'       => __( 'Dimensione titolo H1 (rem)', 'poetheme' ),
                'description' => __( 'Applica una dimensione personalizzata al titolo H1 misurata in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
                'selectors'   => array( 'h1' ),
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h1_spacing',
                'label'       => __( 'Margini e padding H1 (px)', 'poetheme' ),
                'description' => __( 'Inserisci valori in px, ad esempio 24px, per gestire margini e padding del titolo H1.', 'poetheme' ),
                'selectors'   => array( 'h1' ),
            ),
        ),
        'heading_h2_color' => array(
            'option_key'      => 'heading_h2_font',
            'label'           => __( 'Font titolo H2', 'poetheme' ),
            'description'     => __( 'Font per i titoli di secondo livello.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H2', 'poetheme' ),
            'selectors'       => array( 'h2' ),
            'size'            => array(
                'option_key'  => 'heading_h2_font_size',
                'label'       => __( 'Dimensione titolo H2 (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione dei titoli H2 in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h2_spacing',
                'label'       => __( 'Margini e padding H2 (px)', 'poetheme' ),
                'description' => __( 'Usa valori in px, come 18px, per definire margini e padding dei titoli H2.', 'poetheme' ),
                'selectors'   => array( 'h2' ),
            ),
        ),
        'heading_h3_color' => array(
            'option_key'      => 'heading_h3_font',
            'label'           => __( 'Font titolo H3', 'poetheme' ),
            'description'     => __( 'Font per i titoli di terzo livello.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H3', 'poetheme' ),
            'selectors'       => array( 'h3' ),
            'size'            => array(
                'option_key'  => 'heading_h3_font_size',
                'label'       => __( 'Dimensione titolo H3 (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione dei titoli H3 in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h3_spacing',
                'label'       => __( 'Margini e padding H3 (px)', 'poetheme' ),
                'description' => __( 'Specificare valori in px, ad esempio 16px, per regolare margini e padding dei titoli H3.', 'poetheme' ),
                'selectors'   => array( 'h3' ),
            ),
        ),
        'heading_h4_color' => array(
            'option_key'      => 'heading_h4_font',
            'label'           => __( 'Font titolo H4', 'poetheme' ),
            'description'     => __( 'Font per i titoli di quarto livello.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H4', 'poetheme' ),
            'selectors'       => array( 'h4' ),
            'size'            => array(
                'option_key'  => 'heading_h4_font_size',
                'label'       => __( 'Dimensione titolo H4 (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione dei titoli H4 in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h4_spacing',
                'label'       => __( 'Margini e padding H4 (px)', 'poetheme' ),
                'description' => __( 'Indica valori in px, per esempio 14px, per i margini e il padding dei titoli H4.', 'poetheme' ),
                'selectors'   => array( 'h4' ),
            ),
        ),
        'heading_h5_color' => array(
            'option_key'      => 'heading_h5_font',
            'label'           => __( 'Font titolo H5', 'poetheme' ),
            'description'     => __( 'Font per i titoli di quinto livello.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H5', 'poetheme' ),
            'selectors'       => array( 'h5' ),
            'size'            => array(
                'option_key'  => 'heading_h5_font_size',
                'label'       => __( 'Dimensione titolo H5 (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione dei titoli H5 in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h5_spacing',
                'label'       => __( 'Margini e padding H5 (px)', 'poetheme' ),
                'description' => __( 'Inserisci valori in px, come 12px, per controllare margini e padding dei titoli H5.', 'poetheme' ),
                'selectors'   => array( 'h5' ),
            ),
        ),
        'heading_h6_color' => array(
            'option_key'      => 'heading_h6_font',
            'label'           => __( 'Font titolo H6', 'poetheme' ),
            'description'     => __( 'Font per i titoli di sesto livello.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di esempio H6', 'poetheme' ),
            'selectors'       => array( 'h6' ),
            'size'            => array(
                'option_key'  => 'heading_h6_font_size',
                'label'       => __( 'Dimensione titolo H6 (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione dei titoli H6 in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 4,
                'step'        => 0.05,
            ),
            'spacing'         => array(
                'option_key'  => 'heading_h6_spacing',
                'label'       => __( 'Margini e padding H6 (px)', 'poetheme' ),
                'description' => __( 'Usa valori in px, ad esempio 10px, per i margini e il padding dei titoli H6.', 'poetheme' ),
                'selectors'   => array( 'h6' ),
            ),
        ),
        'page_title_color' => array(
            'option_key'      => 'page_title_font',
            'label'           => __( 'Font titolo pagina', 'poetheme' ),
            'description'     => __( 'Scegli il font per il titolo principale delle pagine.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di pagina di esempio', 'poetheme' ),
            'selectors'       => array( '.poetheme-page-title' ),
            'size'            => array(
                'option_key'  => 'page_title_font_size',
                'label'       => __( 'Dimensione titolo pagina (rem)', 'poetheme' ),
                'description' => __( 'Imposta la dimensione del titolo delle pagine in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 6,
                'step'        => 0.05,
            ),
        ),
        'post_title_color' => array(
            'option_key'      => 'post_title_font',
            'label'           => __( 'Font titolo articolo', 'poetheme' ),
            'description'     => __( 'Definisci il font utilizzato per il titolo degli articoli singoli.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di articolo di esempio', 'poetheme' ),
            'selectors'       => array( '.poetheme-post-title' ),
            'size'            => array(
                'option_key'  => 'post_title_font_size',
                'label'       => __( 'Dimensione titolo articolo (rem)', 'poetheme' ),
                'description' => __( 'Personalizza la dimensione del titolo degli articoli in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 6,
                'step'        => 0.05,
            ),
        ),
        'category_title_color' => array(
            'option_key'      => 'category_title_font',
            'label'           => __( 'Font titolo categoria', 'poetheme' ),
            'description'     => __( 'Imposta il font per i titoli delle categorie e delle tassonomie.', 'poetheme' ),
            'default_label'   => __( 'Usa il font generale dei titoli', 'poetheme' ),
            'fallback_key'    => 'heading_fallback',
            'preview_variant' => 'heading',
            'sample'          => __( 'Titolo di categoria di esempio', 'poetheme' ),
            'selectors'       => array( '.poetheme-category-title' ),
            'size'            => array(
                'option_key'  => 'category_title_font_size',
                'label'       => __( 'Dimensione titolo categoria (rem)', 'poetheme' ),
                'description' => __( 'Regola la dimensione del titolo delle pagine categoria in rem.', 'poetheme' ),
                'min'         => 0.8,
                'max'         => 6,
                'step'        => 0.05,
            ),
        ),
    );
}

function poetheme_render_fonts_page() {
    $options          = poetheme_get_font_options();
    $available_fonts  = poetheme_get_available_fonts();
    $font_faces       = poetheme_generate_font_face_css();
    $color_groups     = poetheme_get_color_section_groups();
    $font_fields      = poetheme_get_font_field_config();
    $fonts_for_script = array();

    if ( isset( $color_groups['surfaces'] ) ) {
        $color_groups['surfaces']['title']       = __( 'Tipi di carattere', 'poetheme' );
        $color_groups['surfaces']['description'] = __( 'Gestisci i font principali del sito.', 'poetheme' );
    }

    if ( isset( $color_groups['footer'] ) ) {
        $color_groups['footer']['description'] = __( 'Personalizza i font delle aree widget del piè di pagina.', 'poetheme' );
    }

    foreach ( $available_fonts as $font ) {
        $fonts_for_script[ $font['slug'] ] = $font['family'];
    }

    $prepared_groups   = array();
    $extra_font_fields = array(
        'footer_widgets' => array(
            'footer_widget_heading_color',
            'footer_widget_heading_h2_font',
            'footer_widget_heading_h3_font',
            'footer_widget_heading_h4_font',
            'footer_widget_heading_h5_font',
        ),
    );

    foreach ( $color_groups as $group_key => $group ) {
        $prepared_sections = array();

        foreach ( $group['sections'] as $section_key => $section ) {
            $prepared_fields = array();

            foreach ( $section['fields'] as $field_key => $field ) {
                if ( ! isset( $font_fields[ $field_key ] ) ) {
                    continue;
                }

                $prepared_fields[ $field_key ] = $font_fields[ $field_key ];
            }

            if ( isset( $extra_font_fields[ $section_key ] ) ) {
                foreach ( $extra_font_fields[ $section_key ] as $additional_key ) {
                    if ( isset( $font_fields[ $additional_key ] ) && ! isset( $prepared_fields[ $additional_key ] ) ) {
                        $prepared_fields[ $additional_key ] = $font_fields[ $additional_key ];
                    }
                }
            }

            if ( $prepared_fields ) {
                $prepared_sections[ $section_key ] = array(
                    'title'       => $section['title'],
                    'description' => isset( $section['description'] ) ? $section['description'] : '',
                    'fields'      => $prepared_fields,
                );
            }
        }

        if ( $prepared_sections ) {
            $prepared_groups[ $group_key ] = array(
                'title'       => $group['title'],
                'description' => isset( $group['description'] ) ? $group['description'] : '',
                'sections'    => $prepared_sections,
            );
        }
    }
    ?>
    <div class="wrap poetheme-admin poetheme-font-settings">
        <h1><?php esc_html_e( 'Gestione Font', 'poetheme' ); ?></h1>

        <?php if ( $font_faces ) : ?>
            <style id="poetheme-font-settings-admin">
                <?php echo $font_faces; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </style>
        <?php endif; ?>

        <?php if ( empty( $available_fonts ) ) : ?>
            <div class="notice notice-warning">
                <p><?php esc_html_e( 'Non sono stati trovati font personalizzati nella cartella theme-font. Carica file .woff2, .woff, .ttf o .otf per renderli disponibili.', 'poetheme' ); ?></p>
            </div>
        <?php endif; ?>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_fonts_group' ); ?>

            <?php if ( ! empty( $prepared_groups ) ) : ?>
                <div class="poetheme-font-groups">
                    <?php foreach ( $prepared_groups as $group_key => $group ) : ?>
                        <section class="poetheme-font-group" id="poetheme-font-group-<?php echo esc_attr( $group_key ); ?>">
                            <header class="poetheme-font-group__header">
                                <h2><?php echo esc_html( $group['title'] ); ?></h2>
                                <?php if ( ! empty( $group['description'] ) ) : ?>
                                    <p class="description"><?php echo esc_html( $group['description'] ); ?></p>
                                <?php endif; ?>
                            </header>

                            <div class="poetheme-font-group__sections">
                                <?php foreach ( $group['sections'] as $section_key => $section ) : ?>
                                    <fieldset class="poetheme-font-section" id="poetheme-font-section-<?php echo esc_attr( $section_key ); ?>">
                                        <legend class="poetheme-font-section__title"><?php echo esc_html( $section['title'] ); ?></legend>
                                        <?php if ( ! empty( $section['description'] ) ) : ?>
                                            <p class="description poetheme-font-section__description"><?php echo esc_html( $section['description'] ); ?></p>
                                        <?php endif; ?>

                                        <?php
                                        $field_entries    = array();
                                        $has_right_column = false;

                                        foreach ( $section['fields'] as $field_key => $field ) :
                                            $option_key     = $field['option_key'];
                                            $value          = isset( $options[ $option_key ] ) ? $options[ $option_key ] : '';
                                            $field_id       = 'poetheme-font-' . $option_key;
                                            $field_name     = 'poetheme_fonts[' . $option_key . ']';
                                            $preview_id     = $field_id . '-preview';
                                            $fallback_id    = '';
                                            $fallback_value = '';
                                            $preview_class  = 'poetheme-font-preview';
                                            $size_config    = isset( $field['size'] ) ? $field['size'] : array();
                                            $size_value     = '';
                                            $spacing_config = isset( $field['spacing'] ) ? $field['spacing'] : array();
                                            $spacing_value  = array();
                                            $column         = isset( $field['column'] ) ? $field['column'] : '';
                                            $description_id = ! empty( $field['description'] ) ? $field_id . '-description' : '';

                                            if ( 'right' === $column ) {
                                                $has_right_column = true;
                                            }

                                            if ( ! empty( $field['preview_variant'] ) ) {
                                                $preview_class .= ' poetheme-font-preview--' . sanitize_html_class( $field['preview_variant'] );
                                            }

                                            if ( ! empty( $field['fallback_key'] ) ) {
                                                $fallback_id    = 'poetheme-font-' . $field['fallback_key'];
                                                $fallback_value = isset( $options[ $field['fallback_key'] ] ) ? $options[ $field['fallback_key'] ] : '';
                                            }

                                            $preview_style = '';
                                            if ( $value || $fallback_value ) {
                                                $stack = poetheme_build_font_stack( $value, $fallback_value, $available_fonts );
                                                if ( $stack ) {
                                                    $preview_style = 'font-family:' . $stack . ';';
                                                }
                                            }

                                            if ( ! empty( $size_config['option_key'] ) ) {
                                                $size_key   = $size_config['option_key'];
                                                $size_value = isset( $options[ $size_key ] ) ? $options[ $size_key ] : '';

                                                if ( '' !== $size_value && is_numeric( $size_value ) ) {
                                                    $preview_style .= 'font-size:' . poetheme_format_number_for_css( $size_value ) . 'rem;';
                                                }
                                            }

                                            $fallback_attr = $fallback_id ? ' data-fallback="' . esc_attr( $fallback_id ) . '"' : '';

                                            ob_start();
                                            ?>
                                            <div class="poetheme-font-section__field">
                                                <label class="poetheme-font-section__label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                                <div class="poetheme-font-section__control">
                                                    <select class="poetheme-font-select" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" data-preview="<?php echo esc_attr( $preview_id ); ?>"<?php echo $fallback_attr; ?> <?php disabled( empty( $available_fonts ) ); ?><?php echo $description_id ? ' aria-describedby="' . esc_attr( $description_id ) . '"' : ''; ?>>
                                                        <option value="" <?php selected( '', $value ); ?>><?php echo esc_html( $field['default_label'] ); ?></option>
                                                        <?php foreach ( $available_fonts as $font ) : ?>
                                                            <option value="<?php echo esc_attr( $font['slug'] ); ?>" <?php selected( $value, $font['slug'] ); ?> data-font-family="<?php echo esc_attr( $font['family'] ); ?>">
                                                                <?php echo esc_html( $font['family'] ); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ( ! empty( $field['description'] ) ) : ?>
                                                        <p id="<?php echo esc_attr( $description_id ); ?>" class="description"><?php echo esc_html( $field['description'] ); ?></p>
                                                    <?php endif; ?>
                                                    <div class="<?php echo esc_attr( $preview_class ); ?>" id="<?php echo esc_attr( $preview_id ); ?>" style="<?php echo esc_attr( $preview_style ); ?>">
                                                        <span class="poetheme-font-preview__label"><?php esc_html_e( 'Anteprima', 'poetheme' ); ?></span>
                                                        <span class="poetheme-font-preview__sample"><?php echo esc_html( $field['sample'] ); ?></span>
                                                        <?php if ( isset( $field['preview_variant'] ) && 'heading' === $field['preview_variant'] ) : ?>
                                                            <span class="poetheme-font-preview__spacing-note"><?php esc_html_e( 'Spaziatura, padding e margin (specifica l\'unità di misura da utilizzare con un esempio).', 'poetheme' ); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ( ! empty( $size_config ) && ! empty( $size_config['option_key'] ) ) :
                                                        $size_key        = $size_config['option_key'];
                                                        $size_id         = 'poetheme-font-' . $size_key;
                                                        $size_value_attr = ( '' !== $size_value && is_numeric( $size_value ) ) ? poetheme_format_number_for_css( $size_value ) : '';
                                                        $size_step       = isset( $size_config['step'] ) ? $size_config['step'] : '0.1';
                                                        $size_attrs      = '';

                                                        if ( isset( $size_config['min'] ) ) {
                                                            $size_attrs .= ' min="' . esc_attr( $size_config['min'] ) . '"';
                                                        }

                                                        if ( isset( $size_config['max'] ) ) {
                                                            $size_attrs .= ' max="' . esc_attr( $size_config['max'] ) . '"';
                                                        }
                                                    ?>
                                                        <label class="poetheme-font-size-label" for="<?php echo esc_attr( $size_id ); ?>"><?php echo esc_html( $size_config['label'] ); ?></label>
                                                        <input
                                                            type="number"
                                                            class="small-text poetheme-font-size-control"
                                                            id="<?php echo esc_attr( $size_id ); ?>"
                                                            name="poetheme_fonts[<?php echo esc_attr( $size_key ); ?>]"
                                                            value="<?php echo esc_attr( $size_value_attr ); ?>"
                                                            step="<?php echo esc_attr( $size_step ); ?>"<?php echo $size_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                                            data-preview="<?php echo esc_attr( $preview_id ); ?>"
                                                            data-unit="rem"
                                                            data-property="fontSize"
                                                            <?php echo ! empty( $size_config['description'] ) ? ' aria-describedby="' . esc_attr( $size_id ) . '-description"' : ''; ?>
                                                        />
                                                        <?php if ( ! empty( $size_config['description'] ) ) : ?>
                                                            <p id="<?php echo esc_attr( $size_id ); ?>-description" class="description"><?php echo esc_html( $size_config['description'] ); ?></p>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ( ! empty( $field['border_radius'] ) && ! empty( $field['border_radius']['option_key'] ) ) :
                                                        $radius_config   = $field['border_radius'];
                                                        $radius_key      = $radius_config['option_key'];
                                                        $radius_id       = 'poetheme-font-' . $radius_key;
                                                        $radius_value    = isset( $options[ $radius_key ] ) ? $options[ $radius_key ] : '';
                                                        $radius_value_attr = ( '' !== $radius_value || '0' === (string) $radius_value ) && is_numeric( $radius_value ) ? poetheme_format_number_for_css( $radius_value ) : '';
                                                        $radius_step     = isset( $radius_config['step'] ) ? $radius_config['step'] : '1';
                                                        $radius_attrs    = '';

                                                        if ( isset( $radius_config['min'] ) ) {
                                                            $radius_attrs .= ' min="' . esc_attr( $radius_config['min'] ) . '"';
                                                        }

                                                        if ( isset( $radius_config['max'] ) ) {
                                                            $radius_attrs .= ' max="' . esc_attr( $radius_config['max'] ) . '"';
                                                        }

                                                        $radius_unit = isset( $radius_config['unit'] ) ? $radius_config['unit'] : 'px';
                                                    ?>
                                                        <label class="poetheme-font-size-label" for="<?php echo esc_attr( $radius_id ); ?>"><?php echo esc_html( $radius_config['label'] ); ?></label>
                                                        <input
                                                            type="number"
                                                            class="small-text poetheme-font-size-control poetheme-font-radius-control"
                                                            id="<?php echo esc_attr( $radius_id ); ?>"
                                                            name="poetheme_fonts[<?php echo esc_attr( $radius_key ); ?>]"
                                                            value="<?php echo esc_attr( $radius_value_attr ); ?>"
                                                            step="<?php echo esc_attr( $radius_step ); ?>"<?php echo $radius_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                                            data-preview="<?php echo esc_attr( $preview_id ); ?>"
                                                            data-unit="<?php echo esc_attr( $radius_unit ); ?>"
                                                            data-property="borderRadius"
                                                            <?php echo ! empty( $radius_config['description'] ) ? ' aria-describedby="' . esc_attr( $radius_id ) . '-description"' : ''; ?>
                                                        />
                                                        <?php if ( ! empty( $radius_config['description'] ) ) : ?>
                                                            <p id="<?php echo esc_attr( $radius_id ); ?>-description" class="description"><?php echo esc_html( $radius_config['description'] ); ?></p>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php if ( ! empty( $spacing_config['option_key'] ) ) :
                                                        $spacing_key   = $spacing_config['option_key'];
                                                        $spacing_id    = 'poetheme-font-' . $spacing_key;
                                                        $spacing_value = isset( $options[ $spacing_key ] ) && is_array( $options[ $spacing_key ] ) ? $options[ $spacing_key ] : poetheme_get_default_spacing_group();
                                                        $segments      = array(
                                                            'margin'  => __( 'Margine', 'poetheme' ),
                                                            'padding' => __( 'Padding', 'poetheme' ),
                                                        );
                                                        $directions    = array(
                                                            'top'    => __( 'Alto', 'poetheme' ),
                                                            'right'  => __( 'Destra', 'poetheme' ),
                                                            'bottom' => __( 'Basso', 'poetheme' ),
                                                            'left'   => __( 'Sinistra', 'poetheme' ),
                                                        );
                                                    ?>
                                                        <div class="poetheme-font-spacing">
                                                            <span class="poetheme-font-spacing__label" id="<?php echo esc_attr( $spacing_id ); ?>-label"><?php echo esc_html( $spacing_config['label'] ); ?></span>
                                                            <div class="poetheme-spacing-control" role="group" aria-labelledby="<?php echo esc_attr( $spacing_id ); ?>-label"<?php echo ! empty( $spacing_config['description'] ) ? ' aria-describedby="' . esc_attr( $spacing_id ) . '-description"' : ''; ?>>
                                                                <?php foreach ( $segments as $segment_key => $segment_label ) :
                                                                    $segment_values = isset( $spacing_value[ $segment_key ] ) && is_array( $spacing_value[ $segment_key ] ) ? $spacing_value[ $segment_key ] : array();
                                                                    ?>
                                                                    <div class="poetheme-spacing-row">
                                                                        <span class="poetheme-spacing-row__label"><?php echo esc_html( $segment_label ); ?></span>
                                                                        <div class="poetheme-spacing-row__inputs">
                                                                            <?php foreach ( $directions as $direction_key => $direction_label ) :
                                                                                $input_id    = $spacing_id . '-' . $segment_key . '-' . $direction_key;
                                                                                $input_name  = 'poetheme_fonts[' . $spacing_key . '][' . $segment_key . '][' . $direction_key . ']';
                                                                                $input_value = isset( $segment_values[ $direction_key ] ) ? $segment_values[ $direction_key ] : '';
                                                                                ?>
                                                                                <label class="poetheme-spacing-input" for="<?php echo esc_attr( $input_id ); ?>">
                                                                                    <span class="poetheme-spacing-input__label"><?php echo esc_html( $direction_label ); ?></span>
                                                                                    <input
                                                                                        type="text"
                                                                                        id="<?php echo esc_attr( $input_id ); ?>"
                                                                                        name="<?php echo esc_attr( $input_name ); ?>"
                                                                                        value="<?php echo esc_attr( $input_value ); ?>"
                                                                                        placeholder="<?php echo esc_attr__( 'es. 20px', 'poetheme' ); ?>"
                                                                                        class="poetheme-font-spacing-input-field"
                                                                                        data-preview="<?php echo esc_attr( $preview_id ); ?>"
                                                                                        data-segment="<?php echo esc_attr( $segment_key ); ?>"
                                                                                        data-direction="<?php echo esc_attr( $direction_key ); ?>"
                                                                                    />
                                                                                </label>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php if ( ! empty( $spacing_config['description'] ) ) : ?>
                                                            <p id="<?php echo esc_attr( $spacing_id ); ?>-description" class="description"><?php echo esc_html( $spacing_config['description'] ); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php endif; ?>
                                                    <?php if ( ! empty( $field['fallback'] ) ) :
                                                        $fallback_config = $field['fallback'];
                                                    ?>
                                                        <label class="poetheme-font-fallback-label" for="<?php echo esc_attr( $fallback_id ); ?>"><?php echo esc_html( $fallback_config['label'] ); ?></label>
                                                        <input type="text" class="regular-text poetheme-font-fallback" id="<?php echo esc_attr( $fallback_id ); ?>" name="poetheme_fonts[<?php echo esc_attr( $fallback_config['key'] ); ?>]" value="<?php echo esc_attr( $fallback_value ); ?>" aria-describedby="<?php echo esc_attr( $fallback_id ); ?>-description" />
                                                        <p id="<?php echo esc_attr( $fallback_id ); ?>-description" class="description"><?php echo esc_html( $fallback_config['description'] ); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php
                                            $field_entries[] = array(
                                                'column' => in_array( $column, array( 'left', 'right' ), true ) ? $column : 'left',
                                                'html'   => ob_get_clean(),
                                            );
                                        endforeach;

                                        if ( $has_right_column ) {
                                            $left_entries  = array();
                                            $right_entries = array();

                                            foreach ( $field_entries as $entry ) {
                                                if ( 'right' === $entry['column'] ) {
                                                    $right_entries[] = $entry;
                                                } else {
                                                    $left_entries[] = $entry;
                                                }
                                            }

                                            echo '<div class="poetheme-font-section__fields poetheme-font-section__fields--columns">';
                                            echo '<div class="poetheme-font-section__column poetheme-font-section__column--left">';
                                            foreach ( $left_entries as $entry ) {
                                                echo $entry['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            }
                                            echo '</div>';
                                            echo '<div class="poetheme-font-section__column poetheme-font-section__column--right">';
                                            foreach ( $right_entries as $entry ) {
                                                echo $entry['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            }
                                            echo '</div>';
                                            echo '</div>';
                                        } else {
                                            echo '<div class="poetheme-font-section__fields">';
                                            foreach ( $field_entries as $entry ) {
                                                echo $entry['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            }
                                            echo '</div>';
                                        }
                                        ?>
                                    </fieldset>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e( 'Non sono disponibili sezioni font da configurare.', 'poetheme' ); ?></p>
            <?php endif; ?>

            <?php submit_button(); ?>
        </form>

        <?php if ( ! empty( $available_fonts ) ) : ?>
            <div class="poetheme-font-collection">
                <h2><?php esc_html_e( 'Font disponibili', 'poetheme' ); ?></h2>
                <p class="description"><?php esc_html_e( "L'elenco seguente mostra i font caricati nella cartella theme-font.", 'poetheme' ); ?></p>
                <ul>
                    <?php foreach ( $available_fonts as $font ) : ?>
                        <li><?php echo esc_html( $font['family'] ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <script>
            ( function () {
                const fontMap = <?php echo wp_json_encode( $fonts_for_script ); ?>;

                function buildStack( slug, fallback ) {
                    const parts = [];

                    if ( slug && fontMap[ slug ] ) {
                        parts.push( '\'' + fontMap[ slug ].replace(/'/g, '\\'') + '\'' );
                    }

                    if ( fallback ) {
                        fallback.split( ',' ).forEach( function ( item ) {
                            const trimmed = item.trim();

                            if ( ! trimmed ) {
                                return;
                            }

                            if ( /^(\'|\").*(\1)$/.test( trimmed ) ) {
                                parts.push( trimmed );
                            } else if ( trimmed.indexOf( ' ' ) !== -1 ) {
                                parts.push( '\'' + trimmed.replace(/'/g, '\\'') + '\'' );
                            } else {
                                parts.push( trimmed );
                            }
                        } );
                    }

                    return parts.join( ', ' );
                }

                function updatePreviewForSelect( select ) {
                    if ( ! select ) {
                        return;
                    }

                    const previewId = select.getAttribute( 'data-preview' );

                    if ( ! previewId ) {
                        return;
                    }

                    const preview = document.getElementById( previewId );

                    if ( ! preview ) {
                        return;
                    }

                    const fallbackId = select.getAttribute( 'data-fallback' );
                    let fallbackValue = '';

                    if ( fallbackId ) {
                        const fallbackInput = document.getElementById( fallbackId );

                        if ( fallbackInput ) {
                            fallbackValue = fallbackInput.value;
                        }
                    }

                    const stack = buildStack( select.value, fallbackValue );
                    preview.style.fontFamily = stack || '';
                }

                function updatePreviewSize( input ) {
                    if ( ! input ) {
                        return;
                    }

                    const previewId = input.getAttribute( 'data-preview' );

                    if ( ! previewId ) {
                        return;
                    }

                    const preview = document.getElementById( previewId );

                    if ( ! preview ) {
                        return;
                    }

                    const property = input.getAttribute( 'data-property' ) || 'fontSize';
                    const unit = input.getAttribute( 'data-unit' ) || '';
                    const rawValue = input.value ? input.value.trim() : '';

                    if ( '' === rawValue ) {
                        preview.style[ property ] = '';
                        return;
                    }

                    const numeric = parseFloat( rawValue.replace( ',', '.' ) );

                    if ( Number.isFinite( numeric ) ) {
                        preview.style[ property ] = numeric + unit;
                    } else {
                        preview.style[ property ] = '';
                    }
                }

                function updatePreviewSpacing( input ) {
                    if ( ! input ) {
                        return;
                    }

                    const previewId = input.getAttribute( 'data-preview' );

                    if ( ! previewId ) {
                        return;
                    }

                    const preview = document.getElementById( previewId );

                    if ( ! preview ) {
                        return;
                    }

                    const control = input.closest( '.poetheme-spacing-control' );

                    if ( ! control ) {
                        return;
                    }

                    const fields = control.querySelectorAll( '.poetheme-font-spacing-input-field' );

                    fields.forEach( function ( field ) {
                        const segment = field.getAttribute( 'data-segment' );
                        const direction = field.getAttribute( 'data-direction' );

                        if ( ! segment || ! direction ) {
                            return;
                        }

                        const property = segment + direction.charAt( 0 ).toUpperCase() + direction.slice( 1 );
                        const rawValue = field.value ? field.value.trim() : '';

                        preview.style[ property ] = rawValue ? rawValue : '';
                    } );
                }

                const fontSelects = document.querySelectorAll( '.poetheme-font-select' );
                fontSelects.forEach( function ( select ) {
                    select.addEventListener( 'change', function () {
                        updatePreviewForSelect( select );
                    } );

                    updatePreviewForSelect( select );
                } );

                const fallbackInputs = document.querySelectorAll( '.poetheme-font-fallback' );
                fallbackInputs.forEach( function ( input ) {
                    input.addEventListener( 'input', function () {
                        const fallbackId = input.id;
                        document.querySelectorAll( '.poetheme-font-select[data-fallback="' + fallbackId + '"]' ).forEach( function ( select ) {
                            updatePreviewForSelect( select );
                        } );
                    } );
                } );

                const sizeInputs = document.querySelectorAll( '.poetheme-font-size-control' );
                sizeInputs.forEach( function ( input ) {
                    input.addEventListener( 'input', function () {
                        updatePreviewSize( input );
                    } );

                    updatePreviewSize( input );
                } );

                const spacingInputs = document.querySelectorAll( '.poetheme-font-spacing-input-field' );
                spacingInputs.forEach( function ( input ) {
                    input.addEventListener( 'input', function () {
                        updatePreviewSpacing( input );
                    } );

                    updatePreviewSpacing( input );
                } );
            }() );
        </script>
    </div>
    <?php
}
