<?php
/**
 * Theme options pages and helpers.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Retrieve the available header social networks definitions.
 *
 * @return array
 */
function poetheme_get_header_social_networks() {
    return array(
        'facebook'  => array(
            'label' => __( 'Facebook', 'poetheme' ),
            'icon'  => 'facebook',
        ),
        'instagram' => array(
            'label' => __( 'Instagram', 'poetheme' ),
            'icon'  => 'instagram',
        ),
        'youtube'   => array(
            'label' => __( 'YouTube', 'poetheme' ),
            'icon'  => 'youtube',
        ),
        'linkedin'  => array(
            'label' => __( 'LinkedIn', 'poetheme' ),
            'icon'  => 'linkedin',
        ),
    );
}

/**
 * Default values for header options.
 *
 * @return array
 */
function poetheme_get_default_header_options() {
    $social_defaults = array();

    foreach ( poetheme_get_header_social_networks() as $key => $data ) {
        $social_defaults[ $key ] = '';
    }

    return array(
        'layout'        => 'style-1',
        'show_top_bar'  => true,
        'show_cta'      => true,
        'top_bar_texts' => array(
            'text_1'  => '',
            'email'   => '',
            'phone'   => '',
            'whatsapp'=> '',
            'location_label' => '',
            'location_url'   => '',
        ),
        'cta_text'      => __( 'Get Started', 'poetheme' ),
        'cta_url'       => home_url( '/' ),
        'social_links'  => $social_defaults,
    );
}

/**
 * Retrieve the available footer layout choices.
 *
 * @return array
 */
function poetheme_get_footer_layout_choices() {
    return array(
        'four-equal'            => array(
            'label'   => __( '1/4 – 1/4 – 1/4 – 1/4', 'poetheme' ),
            'columns' => array( 3, 3, 3, 3 ),
        ),
        'half-quarter-quarter'  => array(
            'label'   => __( '1/2 – 1/4 – 1/4', 'poetheme' ),
            'columns' => array( 6, 3, 3 ),
        ),
        'quarter-quarter-half'  => array(
            'label'   => __( '1/4 – 1/4 – 1/2', 'poetheme' ),
            'columns' => array( 3, 3, 6 ),
        ),
        'half-half'             => array(
            'label'   => __( '1/2 – 1/2', 'poetheme' ),
            'columns' => array( 6, 6 ),
        ),
        'full-width'            => array(
            'label'   => __( '1/1', 'poetheme' ),
            'columns' => array( 12 ),
        ),
    );
}

/**
 * Retrieve the default footer options.
 *
 * @return array
 */
function poetheme_get_default_footer_options() {
    return array(
        'display_footer' => true,
        'display_footer_credits' => true,
        'credits_content' => '',
        'rows'        => 1,
        'row_layouts' => array(
            1 => 'four-equal',
            2 => 'half-half',
        ),
    );
}

/**
 * Retrieve saved footer options merged with defaults.
 *
 * @return array
 */
function poetheme_get_footer_options() {
    $options  = get_option( 'poetheme_footer', array() );
    $defaults = poetheme_get_default_footer_options();

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    if ( ! isset( $options['row_layouts'] ) || ! is_array( $options['row_layouts'] ) ) {
        $options['row_layouts'] = $defaults['row_layouts'];
    }

    $options['display_footer'] = ! empty( $options['display_footer'] );
    $options['display_footer_credits'] = ! empty( $options['display_footer_credits'] );
    $options['credits_content'] = isset( $options['credits_content'] ) ? (string) $options['credits_content'] : '';

    return $options;
}

/**
 * Register theme settings.
 */
function poetheme_get_default_global_options() {
    return array(
        'layout_mode' => 'full',
        'site_width'  => 1200,
        'background_image_id' => 0,
        'background_position' => 'no-repeat;left top;;',
        'background_size'     => 'auto',
    );
}

function poetheme_get_default_color_options() {
    return array(
        'content_text_color'             => '#111827',
        'content_link_color'             => '#2563eb',
        'content_link_underline'         => false,
        'content_strong_color'           => '#111827',
        'page_background_color'          => '#f9fafb',
        'content_background_color'       => '#ffffff',
        'header_background_color'        => '#ffffff',
        'header_background_transparent'  => false,
        'header_disable_shadow'          => false,
        'menu_link_color'                => '#374151',
        'menu_link_background_color'     => '',
        'menu_active_link_color'         => '#2563eb',
        'menu_active_link_background'    => '',
        'cta_background_color'           => '#2563eb',
        'cta_text_color'                 => '#ffffff',
        'top_bar_background_color'       => '#111827',
        'top_bar_icon_color'             => '#ffffff',
        'top_bar_text_color'             => '#ffffff',
        'top_bar_link_color'             => '#ffffff',
        'general_link_color'             => '#2563eb',
        'heading_h1_color'               => '#111827',
        'heading_h1_background'          => '',
        'heading_h2_color'               => '#111827',
        'heading_h2_background'          => '',
        'heading_h3_color'               => '#111827',
        'heading_h3_background'          => '',
        'heading_h4_color'               => '#111827',
        'heading_h4_background'          => '',
        'heading_h5_color'               => '#111827',
        'heading_h5_background'          => '',
        'heading_h6_color'               => '#111827',
        'heading_h6_background'          => '',
        'page_title_color'               => '#111827',
        'page_title_background'          => '',
        'post_title_color'               => '#111827',
        'post_title_background'          => '',
        'category_title_color'           => '#111827',
        'category_title_background'      => '',
        'footer_widget_heading_h2_color'      => '',
        'footer_widget_heading_h2_background' => '',
        'footer_widget_heading_h3_color'      => '',
        'footer_widget_heading_h3_background' => '',
        'footer_widget_heading_h4_color'      => '',
        'footer_widget_heading_h4_background' => '',
        'footer_widget_heading_h5_color'      => '',
        'footer_widget_heading_h5_background' => '',
        'footer_widget_text_color'         => '',
        'footer_widget_link_color'         => '',
        'footer_widget_background_color'   => '',
        'footer_widget_background_transparent' => false,
    );
}

/**
 * Retrieve the directory containing custom theme fonts.
 *
 * @return string
 */
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

    return wp_parse_args( $options, poetheme_get_default_font_options() );
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

    $selected_fonts = array();
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

    $selected_fonts = array_values( array_unique( array_filter( $selected_fonts ) ) );

    $font_faces = poetheme_generate_font_face_css( $selected_fonts );

    if ( $size_rules ) {
        $css_rules .= $size_rules;
    }

    if ( $radius_rules ) {
        $css_rules .= $radius_rules;
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
function poetheme_sanitize_global_options( $input ) {
    $defaults = poetheme_get_default_global_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $layout_mode = isset( $input['layout_mode'] ) ? sanitize_key( $input['layout_mode'] ) : $defaults['layout_mode'];
    if ( ! in_array( $layout_mode, array( 'full', 'boxed' ), true ) ) {
        $layout_mode = $defaults['layout_mode'];
    }

    $width = isset( $input['site_width'] ) ? absint( $input['site_width'] ) : $defaults['site_width'];
    $width = max( 960, min( 1920, $width ) );

    $background_image_id = isset( $input['background_image_id'] ) ? absint( $input['background_image_id'] ) : 0;

    $allowed_positions = array(
        '',
        'no-repeat;left top;;',
        'repeat;left top;;',
        'no-repeat;left center;;',
        'repeat;left center;;',
        'no-repeat;left bottom;;',
        'repeat;left bottom;;',
        'no-repeat;center top;;',
        'repeat;center top;;',
        'repeat-x;center top;;',
        'repeat-y;center top;;',
        'no-repeat;center;;',
        'repeat;center;;',
        'no-repeat;center bottom;;',
        'repeat;center bottom;;',
        'repeat-x;center bottom;;',
        'repeat-y;center bottom;;',
        'no-repeat;right top;;',
        'repeat;right top;;',
        'no-repeat;right center;;',
        'repeat;right center;;',
        'no-repeat;right bottom;;',
        'repeat;right bottom;;',
        'no-repeat;center top;fixed;;',
        'no-repeat;center;fixed;cover',
    );

    $background_position = isset( $input['background_position'] ) ? sanitize_text_field( $input['background_position'] ) : $defaults['background_position'];
    if ( ! in_array( $background_position, $allowed_positions, true ) ) {
        $background_position = $defaults['background_position'];
    }

    $allowed_sizes = array( '', 'auto', 'contain', 'cover', 'cover-ultrawide' );
    $background_size = isset( $input['background_size'] ) ? sanitize_text_field( $input['background_size'] ) : $defaults['background_size'];
    if ( ! in_array( $background_size, $allowed_sizes, true ) ) {
        $background_size = $defaults['background_size'];
    }

    return array(
        'layout_mode' => $layout_mode,
        'site_width'  => $width,
        'background_image_id' => $background_image_id,
        'background_position' => $background_position,
        'background_size'     => $background_size,
    );
}

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

function poetheme_sanitize_color_options( $input ) {
    $defaults = poetheme_get_default_color_options();
    $output   = array();
    $boolean_keys = array(
        'content_link_underline',
        'header_background_transparent',
        'header_disable_shadow',
        'footer_widget_background_transparent',
    );

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    foreach ( $defaults as $key => $default_value ) {
        if ( in_array( $key, $boolean_keys, true ) ) {
            $output[ $key ] = ! empty( $input[ $key ] );
            continue;
        }

        if ( ! isset( $input[ $key ] ) ) {
            $output[ $key ] = $default_value;
            continue;
        }

        $raw = (string) $input[ $key ];

        if ( '' === $raw ) {
            $output[ $key ] = '';
            continue;
        }

        if ( poetheme_is_valid_css_color( $raw ) ) {
            $output[ $key ] = poetheme_normalize_color_value( $raw, $default_value );
            continue;
        }

        $output[ $key ] = poetheme_normalize_color_value( $default_value, '' );
    }

    return $output;
}

/**
 * Retrieve global layout options with defaults.
 *
 * @return array
 */
function poetheme_get_global_options() {
    $defaults = poetheme_get_default_global_options();
    $options  = get_option( 'poetheme_global', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $options['layout_mode'] = in_array( $options['layout_mode'], array( 'full', 'boxed' ), true ) ? $options['layout_mode'] : $defaults['layout_mode'];
    $options['site_width']  = max( 960, min( 1920, absint( $options['site_width'] ) ) );
    $options['background_image_id'] = isset( $options['background_image_id'] ) ? absint( $options['background_image_id'] ) : $defaults['background_image_id'];

    $allowed_positions = array(
        '',
        'no-repeat;left top;;',
        'repeat;left top;;',
        'no-repeat;left center;;',
        'repeat;left center;;',
        'no-repeat;left bottom;;',
        'repeat;left bottom;;',
        'no-repeat;center top;;',
        'repeat;center top;;',
        'repeat-x;center top;;',
        'repeat-y;center top;;',
        'no-repeat;center;;',
        'repeat;center;;',
        'no-repeat;center bottom;;',
        'repeat;center bottom;;',
        'repeat-x;center bottom;;',
        'repeat-y;center bottom;;',
        'no-repeat;right top;;',
        'repeat;right top;;',
        'no-repeat;right center;;',
        'repeat;right center;;',
        'no-repeat;right bottom;;',
        'repeat;right bottom;;',
        'no-repeat;center top;fixed;;',
        'no-repeat;center;fixed;cover',
    );

    if ( ! in_array( $options['background_position'], $allowed_positions, true ) ) {
        $options['background_position'] = $defaults['background_position'];
    }

    $allowed_sizes = array( '', 'auto', 'contain', 'cover', 'cover-ultrawide' );
    if ( ! in_array( $options['background_size'], $allowed_sizes, true ) ) {
        $options['background_size'] = $defaults['background_size'];
    }

    return $options;
}

function poetheme_get_color_options() {
    $defaults = poetheme_get_default_color_options();
    $raw      = get_option( 'poetheme_colors', array() );
    $boolean_keys = array(
        'content_link_underline',
        'header_background_transparent',
        'header_disable_shadow',
        'footer_widget_background_transparent',
    );

    if ( ! is_array( $raw ) ) {
        $raw = array();
    }

    $legacy_keys = array(
        'sidebar_widget_text_color'         => 'footer_widget_text_color',
        'sidebar_widget_link_color'         => 'footer_widget_link_color',
        'sidebar_container_background_color'=> 'footer_widget_background_color',
        'sidebar_container_background_transparent' => 'footer_widget_background_transparent',
    );

    foreach ( $legacy_keys as $legacy_key => $current_key ) {
        if ( ! isset( $raw[ $current_key ] ) && isset( $raw[ $legacy_key ] ) ) {
            $raw[ $current_key ] = $raw[ $legacy_key ];
        }
    }

    $legacy_heading_sources = array(
        'sidebar_widget_heading_color',
        'footer_widget_heading_color',
    );

    foreach ( $legacy_heading_sources as $legacy_key ) {
        if ( empty( $raw[ $legacy_key ] ) ) {
            continue;
        }

        foreach ( array( 'footer_widget_heading_h2_color', 'footer_widget_heading_h3_color', 'footer_widget_heading_h4_color', 'footer_widget_heading_h5_color' ) as $target_key ) {
            if ( empty( $raw[ $target_key ] ) ) {
                $raw[ $target_key ] = $raw[ $legacy_key ];
            }
        }
    }

    $legacy_heading_background_sources = array(
        'sidebar_widget_heading_background',
        'footer_widget_heading_background',
    );

    foreach ( $legacy_heading_background_sources as $legacy_key ) {
        if ( empty( $raw[ $legacy_key ] ) ) {
            continue;
        }

        foreach ( array( 'footer_widget_heading_h2_background', 'footer_widget_heading_h3_background', 'footer_widget_heading_h4_background', 'footer_widget_heading_h5_background' ) as $target_key ) {
            if ( empty( $raw[ $target_key ] ) ) {
                $raw[ $target_key ] = $raw[ $legacy_key ];
            }
        }
    }

    $options = array();

    foreach ( $defaults as $key => $default_value ) {
        if ( in_array( $key, $boolean_keys, true ) ) {
            $options[ $key ] = ! empty( $raw[ $key ] );
            continue;
        }

        if ( array_key_exists( $key, $raw ) ) {
            $raw_value = (string) $raw[ $key ];

            if ( '' === $raw_value ) {
                $options[ $key ] = '';
                continue;
            }

            if ( poetheme_is_valid_css_color( $raw_value ) ) {
                $options[ $key ] = poetheme_normalize_color_value( $raw_value, $default_value );
                continue;
            }
        }

        $options[ $key ] = poetheme_normalize_color_value( $default_value, '' );
    }

    return $options;
}

/**
 * Retrieve default subheader options.
 *
 * @return array
 */
function poetheme_get_default_subheader_options() {
    return array(
        'enable_subheader'      => true,
        'show_title'            => true,
        'show_breadcrumbs'      => true,
        'layout'                => 'stack-center',
        'title_tag'             => 'h1',
        'breadcrumbs_separator' => '/',
    );
}

/**
 * Retrieve registered subheader layouts.
 *
 * @return array
 */
function poetheme_get_subheader_layout_choices() {
    return array(
        'stack-center'    => __( 'Titolo e Breadcrumbs centrati (uno sotto l’altro)', 'poetheme' ),
        'stack-left'      => __( 'Titolo e Breadcrumbs a sinistra (uno sotto l’altro)', 'poetheme' ),
        'stack-right'     => __( 'Titolo e Breadcrumbs a destra (uno sotto l’altro)', 'poetheme' ),
        'title-left-only' => __( 'Titolo a sinistra (Breadcrumbs nascosto)', 'poetheme' ),
        'title-right-only'=> __( 'Titolo a destra (Breadcrumbs nascosto)', 'poetheme' ),
        'title-center-only'=> __( 'Titolo al centro (Breadcrumbs nascosto)', 'poetheme' ),
        'split-title-left'=> __( 'Titolo a sinistra e Breadcrumbs a destra su unico rigo (60% / 40%)', 'poetheme' ),
        'split-title-right'=> __( 'Titolo a destra e Breadcrumbs a sinistra su unico rigo (40% / 60%)', 'poetheme' ),
    );
}

/**
 * Retrieve stored subheader options with defaults.
 *
 * @return array
 */
function poetheme_get_subheader_options() {
    $defaults = poetheme_get_default_subheader_options();
    $options  = get_option( 'poetheme_subheader', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $options = wp_parse_args( $options, $defaults );

    $options['enable_subheader'] = ! empty( $options['enable_subheader'] );
    $options['show_title']       = ! empty( $options['show_title'] );
    $options['show_breadcrumbs'] = ! empty( $options['show_breadcrumbs'] );

    $layouts = array_keys( poetheme_get_subheader_layout_choices() );
    if ( ! in_array( $options['layout'], $layouts, true ) ) {
        $options['layout'] = $defaults['layout'];
    }

    $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    if ( ! in_array( strtolower( $options['title_tag'] ), $allowed_tags, true ) ) {
        $options['title_tag'] = $defaults['title_tag'];
    }

    $separator = isset( $options['breadcrumbs_separator'] ) ? (string) $options['breadcrumbs_separator'] : $defaults['breadcrumbs_separator'];
    $separator = wp_strip_all_tags( $separator );
    $separator = trim( $separator );
    if ( '' === $separator ) {
        $separator = $defaults['breadcrumbs_separator'];
    }
    if ( function_exists( 'mb_substr' ) ) {
        $separator = mb_substr( $separator, 0, 10 );
    } else {
        $separator = substr( $separator, 0, 10 );
    }
    $options['breadcrumbs_separator'] = $separator;

    return $options;
}

/**
 * Sanitize subheader options on save.
 *
 * @param array $input Raw values.
 * @return array
 */
function poetheme_sanitize_subheader_options( $input ) {
    $defaults = poetheme_get_default_subheader_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $output = array();

    $output['enable_subheader'] = ! empty( $input['enable_subheader'] );
    $output['show_title']       = ! empty( $input['show_title'] );
    $output['show_breadcrumbs'] = ! empty( $input['show_breadcrumbs'] );

    $layouts = array_keys( poetheme_get_subheader_layout_choices() );
    $layout  = isset( $input['layout'] ) ? sanitize_key( $input['layout'] ) : $defaults['layout'];
    if ( ! in_array( $layout, $layouts, true ) ) {
        $layout = $defaults['layout'];
    }
    $output['layout'] = $layout;

    $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $title_tag     = isset( $input['title_tag'] ) ? strtolower( sanitize_text_field( $input['title_tag'] ) ) : $defaults['title_tag'];
    if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
        $title_tag = $defaults['title_tag'];
    }
    $output['title_tag'] = $title_tag;

    $separator = isset( $input['breadcrumbs_separator'] ) ? wp_strip_all_tags( (string) $input['breadcrumbs_separator'] ) : $defaults['breadcrumbs_separator'];
    $separator = trim( $separator );
    if ( '' === $separator ) {
        $separator = $defaults['breadcrumbs_separator'];
    }
    if ( function_exists( 'mb_substr' ) ) {
        $separator = mb_substr( $separator, 0, 10 );
    } else {
        $separator = substr( $separator, 0, 10 );
    }
    $output['breadcrumbs_separator'] = $separator;

    return $output;
}

/**
 * Sanitize footer options before saving.
 *
 * @param array $input Raw input values.
 * @return array
 */
function poetheme_sanitize_footer_options( $input ) {
    $defaults = poetheme_get_default_footer_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    $output = $defaults;

    $rows = isset( $input['rows'] ) ? (int) $input['rows'] : $defaults['rows'];
    if ( $rows < 1 || $rows > 2 ) {
        $rows = $defaults['rows'];
    }
    $output['rows'] = $rows;

    $choices = poetheme_get_footer_layout_choices();

    $output['row_layouts'] = array();
    for ( $row = 1; $row <= 2; $row++ ) {
        $value = $defaults['row_layouts'][ $row ];

        if ( isset( $input['row_layouts'][ $row ] ) ) {
            $candidate = sanitize_key( $input['row_layouts'][ $row ] );

            if ( isset( $choices[ $candidate ] ) ) {
                $value = $candidate;
            }
        }

        $output['row_layouts'][ $row ] = $value;
    }

    $output['display_footer'] = ! empty( $input['display_footer'] );
    $output['display_footer_credits'] = ! empty( $input['display_footer_credits'] );

    if ( isset( $input['credits_content'] ) ) {
        $output['credits_content'] = wp_kses_post( (string) $input['credits_content'] );
    }

    return $output;
}

/**
 * Retrieve default page settings meta values.
 *
 * @return array
 */
function poetheme_get_default_page_settings() {
    return array(
        'hide_breadcrumbs'   => false,
        'hide_title'         => false,
        'remove_top_padding' => false,
    );
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
        'tsg_render_options_page'
    );
}
add_action( 'admin_menu', 'poetheme_add_options_pages' );

/**
 * Render the global settings page.
 */
function poetheme_render_global_page() {
    $options              = poetheme_get_global_options();
    $layout_mode          = isset( $options['layout_mode'] ) ? $options['layout_mode'] : 'full';
    $site_width           = isset( $options['site_width'] ) ? absint( $options['site_width'] ) : 1200;
    $background_image_id  = isset( $options['background_image_id'] ) ? absint( $options['background_image_id'] ) : 0;
    $background_image     = $background_image_id ? wp_get_attachment_image_src( $background_image_id, 'large' ) : false;
    $background_position  = isset( $options['background_position'] ) ? $options['background_position'] : '';
    $background_size      = isset( $options['background_size'] ) ? $options['background_size'] : 'auto';
    $width_id             = 'poetheme-global-site-width';
    $layout_field         = 'poetheme_global[layout_mode]';
    $background_positions = array(
        ''                              => __( 'Predefinito', 'poetheme' ),
        'no-repeat;left top;;'          => __( 'Sinistra Alto | no-repeat', 'poetheme' ),
        'repeat;left top;;'             => __( 'Sinistra Alto | repeat', 'poetheme' ),
        'no-repeat;left center;;'       => __( 'Sinistra Centro | no-repeat', 'poetheme' ),
        'repeat;left center;;'          => __( 'Sinistra Centro | repeat', 'poetheme' ),
        'no-repeat;left bottom;;'       => __( 'Sinistra Basso | no-repeat', 'poetheme' ),
        'repeat;left bottom;;'          => __( 'Sinistra Basso | repeat', 'poetheme' ),
        'no-repeat;center top;;'        => __( 'Centro Alto | no-repeat', 'poetheme' ),
        'repeat;center top;;'           => __( 'Centro Alto | repeat', 'poetheme' ),
        'repeat-x;center top;;'         => __( 'Centro Alto | repeat-x', 'poetheme' ),
        'repeat-y;center top;;'         => __( 'Centro Alto | repeat-y', 'poetheme' ),
        'no-repeat;center;;'            => __( 'Centro Centro | no-repeat', 'poetheme' ),
        'repeat;center;;'               => __( 'Centro Centro | repeat', 'poetheme' ),
        'no-repeat;center bottom;;'     => __( 'Centro Basso | no-repeat', 'poetheme' ),
        'repeat;center bottom;;'        => __( 'Centro Basso | repeat', 'poetheme' ),
        'repeat-x;center bottom;;'      => __( 'Centro Basso | repeat-x', 'poetheme' ),
        'repeat-y;center bottom;;'      => __( 'Centro Basso | repeat-y', 'poetheme' ),
        'no-repeat;right top;;'         => __( 'Destra Alto | no-repeat', 'poetheme' ),
        'repeat;right top;;'            => __( 'Destra Alto | repeat', 'poetheme' ),
        'no-repeat;right center;;'      => __( 'Destra Centro | no-repeat', 'poetheme' ),
        'repeat;right center;;'         => __( 'Destra Centro | repeat', 'poetheme' ),
        'no-repeat;right bottom;;'      => __( 'Destra Basso | no-repeat', 'poetheme' ),
        'repeat;right bottom;;'         => __( 'Destra Basso | repeat', 'poetheme' ),
        'no-repeat;center top;fixed;;'  => __( 'Centro | no-repeat | fisso', 'poetheme' ),
        'no-repeat;center;fixed;cover'  => __( 'Centro | no-repeat | fisso | copri', 'poetheme' ),
    );

    $background_sizes = array(
        ''               => __( 'Predefinito', 'poetheme' ),
        'auto'           => __( 'Automatico', 'poetheme' ),
        'contain'        => __( 'Contenere', 'poetheme' ),
        'cover'          => __( 'Coprire', 'poetheme' ),
        'cover-ultrawide'=> __( 'Coprire, solo su schermi ultra larghi > 1920px', 'poetheme' ),
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Globale', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_global_group' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Layout', 'poetheme' ); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php esc_html_e( 'Layout', 'poetheme' ); ?></legend>
                                <label>
                                    <input type="radio" name="<?php echo esc_attr( $layout_field ); ?>" value="full" <?php checked( 'full', $layout_mode ); ?>>
                                    <?php esc_html_e( 'Larghezza piena (100% della pagina)', 'poetheme' ); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="radio" name="<?php echo esc_attr( $layout_field ); ?>" value="boxed" <?php checked( 'boxed', $layout_mode ); ?>>
                                    <?php esc_html_e( 'Larghezza box', 'poetheme' ); ?>
                                </label>
                                <p class="description"><?php esc_html_e( 'Scegli come allineare l’intero sito, incluse testata e piè di pagina.', 'poetheme' ); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr id="poetheme-global-width-row">
                        <th scope="row"><label for="<?php echo esc_attr( $width_id ); ?>"><?php esc_html_e( 'Larghezza sito (px)', 'poetheme' ); ?></label></th>
                        <td>
                            <input type="number" name="poetheme_global[site_width]" id="<?php echo esc_attr( $width_id ); ?>" value="<?php echo esc_attr( $site_width ); ?>" min="960" max="1920" step="10" class="small-text">
                            <p class="description"><?php esc_html_e( 'Imposta la larghezza massima del sito per il layout Box. Valori consentiti da 960 a 1920 pixel.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Immagine di sfondo della pagina', 'poetheme' ); ?></th>
                        <td>
                            <div class="poetheme-background-control">
                                <div id="poetheme-background-preview" class="poetheme-background-preview">
                                    <?php if ( $background_image ) : ?>
                                        <img src="<?php echo esc_url( $background_image[0] ); ?>" alt="" />
                                    <?php else : ?>
                                        <p class="description"><?php esc_html_e( 'Nessuna immagine selezionata.', 'poetheme' ); ?></p>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" id="poetheme_global_background_image_id" name="poetheme_global[background_image_id]" value="<?php echo esc_attr( $background_image_id ); ?>">
                                <p class="poetheme-background-actions">
                                    <button type="button" class="button button-secondary" id="poetheme-background-upload"><?php esc_html_e( 'Scegli immagine', 'poetheme' ); ?></button>
                                    <button type="button" class="button" id="poetheme-background-remove" <?php disabled( 0 === $background_image_id ); ?>><?php esc_html_e( 'Rimuovi immagine', 'poetheme' ); ?></button>
                                </p>
                                <p class="description"><?php esc_html_e( 'Dimensioni consigliate: 1920x1080 px.', 'poetheme' ); ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="poetheme-background-position"><?php esc_html_e( 'Posizione e ripetizione', 'poetheme' ); ?></label></th>
                        <td>
                            <select id="poetheme-background-position" name="poetheme_global[background_position]">
                                <?php foreach ( $background_positions as $value => $label ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $background_position ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Seleziona la combinazione desiderata di ripetizione, posizione e (se disponibile) fissaggio.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="poetheme-background-size"><?php esc_html_e( 'Dimensione', 'poetheme' ); ?></label></th>
                        <td>
                            <select id="poetheme-background-size" name="poetheme_global[background_size]">
                                <?php foreach ( $background_sizes as $value => $label ) : ?>
                                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $background_size ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Questa opzione non è compatibile con la posizione fissa nei browser meno recenti.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function() {
            const layoutRadios = document.querySelectorAll('input[name="<?php echo esc_js( $layout_field ); ?>"]');
            const widthRow = document.getElementById('poetheme-global-width-row');

            function toggleWidthRow() {
                if (!widthRow) {
                    return;
                }
                let selected = 'full';
                layoutRadios.forEach(function(radio) {
                    if (radio.checked) {
                        selected = radio.value;
                    }
                });
                widthRow.style.display = selected === 'boxed' ? '' : 'none';
            }

            layoutRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleWidthRow);
            });

            toggleWidthRow();
        })();
    </script>
    <?php
}

function poetheme_get_color_section_groups() {
    return array(
        'surfaces' => array(
            'title'       => __( 'Contenuti e sfondi', 'poetheme' ),
            'description' => __( 'Gestisci i colori principali delle aree di contenuto e dello sfondo del sito.', 'poetheme' ),
            'sections'    => array(
                'content' => array(
                    'title'  => __( 'Contenuto principale', 'poetheme' ),
                    'fields' => array(
                        'content_text_color'       => array(
                            'label'       => __( 'Colore del testo del contenuto', 'poetheme' ),
                            'description' => __( 'Si applica ai testi principali all’interno del contenuto.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'content_link_color'       => array(
                            'label'       => __( 'Colore dei link nel contenuto', 'poetheme' ),
                            'description' => __( 'Personalizza il colore dei collegamenti nel corpo dei contenuti.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'content_link_underline'   => array(
                            'label'       => __( 'Sottolinea i link del contenuto', 'poetheme' ),
                            'description' => __( 'Attiva o disattiva la sottolineatura per i link nel contenuto.', 'poetheme' ),
                            'type'        => 'toggle',
                        ),
                        'content_strong_color'     => array(
                            'label'       => __( 'Colore del testo evidenziato (strong)', 'poetheme' ),
                            'description' => __( 'Imposta il colore per i testi marcati in grassetto.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'page_background_color'    => array(
                            'label'       => __( 'Colore di sfondo dell’intera pagina', 'poetheme' ),
                            'description' => __( 'Utilizza questo colore assieme o in alternativa all’immagine di sfondo.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'content_background_color' => array(
                            'label'       => __( 'Colore di sfondo del contenuto', 'poetheme' ),
                            'description' => __( 'Colore applicato alle aree principali del contenuto.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
                'general' => array(
                    'title'  => __( 'Link globali', 'poetheme' ),
                    'fields' => array(
                        'general_link_color' => array(
                            'label'       => __( 'Colore link generale', 'poetheme' ),
                            'description' => __( 'Colore applicato ai link generici del sito (intestazione, piè di pagina, ecc.).', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
            ),
        ),
        'header' => array(
            'title'       => __( 'Intestazione', 'poetheme' ),
            'description' => __( 'Personalizza la testata, il menù principale e la call to action.', 'poetheme' ),
            'sections'    => array(
                'header_base' => array(
                    'title'  => __( 'Testata', 'poetheme' ),
                    'fields' => array(
                        'header_background_color'       => array(
                            'label'       => __( 'Colore di sfondo della testata', 'poetheme' ),
                            'description' => __( 'Imposta il colore di sfondo del contenitore principale della testata.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'header_background_transparent' => array(
                            'label'       => __( 'Rendi la testata trasparente', 'poetheme' ),
                            'description' => __( 'Rimuove qualsiasi colore di sfondo e rende la testata trasparente.', 'poetheme' ),
                            'type'        => 'toggle',
                        ),
                        'header_disable_shadow'         => array(
                            'label'       => __( 'Rimuovi ombra della testata', 'poetheme' ),
                            'description' => __( 'Disattiva l’ombra presente sotto la testata.', 'poetheme' ),
                            'type'        => 'toggle',
                        ),
                    ),
                ),
                'menu' => array(
                    'title'  => __( 'Menù principale', 'poetheme' ),
                    'fields' => array(
                        'menu_link_color'             => array(
                            'label'       => __( 'Colore link', 'poetheme' ),
                            'description' => __( 'Colore base dei link del menù principale.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'menu_link_background_color'  => array(
                            'label'       => __( 'Colore sfondo link', 'poetheme' ),
                            'description' => __( 'Sfondo dei link del menù principale (desktop e mobile).', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'menu_active_link_color'      => array(
                            'label'       => __( 'Colore del link attivo', 'poetheme' ),
                            'description' => __( 'Colore per la voce di menù attiva o al passaggio del mouse.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'menu_active_link_background' => array(
                            'label'       => __( 'Colore sfondo link attivo', 'poetheme' ),
                            'description' => __( 'Sfondo della voce di menù attiva.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
                'cta' => array(
                    'title'  => __( 'Call to Action', 'poetheme' ),
                    'fields' => array(
                        'cta_background_color' => array(
                            'label'       => __( 'Colore di sfondo', 'poetheme' ),
                            'description' => __( 'Colore del pulsante principale di invito all’azione.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'cta_text_color'       => array(
                            'label'       => __( 'Colore del testo', 'poetheme' ),
                            'description' => __( 'Colore del testo all’interno del pulsante.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
                'top_bar' => array(
                    'title'  => __( 'Barra superiore', 'poetheme' ),
                    'fields' => array(
                        'top_bar_background_color' => array(
                            'label'       => __( 'Colore di sfondo della barra', 'poetheme' ),
                            'description' => __( 'Colore dello sfondo dell’intera barra superiore.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'top_bar_icon_color'       => array(
                            'label'       => __( 'Colore delle icone', 'poetheme' ),
                            'description' => __( 'Si applica alle icone social e di contatto.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'top_bar_text_color'       => array(
                            'label'       => __( 'Colore del testo', 'poetheme' ),
                            'description' => __( 'Colore del testo nella barra superiore.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'top_bar_link_color'       => array(
                            'label'       => __( 'Colore dei link', 'poetheme' ),
                            'description' => __( 'Colore dei collegamenti testuali della barra.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
            ),
        ),
        'footer' => array(
            'title'       => __( 'Piè di pagina', 'poetheme' ),
            'description' => __( 'Personalizza i colori delle aree widget del piè di pagina.', 'poetheme' ),
            'sections'    => array(
                'footer_widgets' => array(
                    'title'       => __( 'Widget Footer', 'poetheme' ),
                    'description' => __( 'Gestisci i colori dei widget mostrati nelle aree del piè di pagina.', 'poetheme' ),
                    'fields'      => array(
                        'footer_widget_text_color' => array(
                            'label'       => __( 'Colore testo widget', 'poetheme' ),
                            'description' => __( 'Personalizza il colore del testo dei widget posizionati nel footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'left',
                        ),
                        'footer_widget_link_color' => array(
                            'label'       => __( 'Colore link widget', 'poetheme' ),
                            'description' => __( 'Imposta il colore dei link all’interno dei widget del footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'left',
                        ),
                        'footer_widget_background_color' => array(
                            'label'       => __( 'Colore sfondo area widget', 'poetheme' ),
                            'description' => __( 'Scegli il colore di sfondo per l’intero blocco dei widget nel footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'left',
                        ),
                        'footer_widget_background_transparent' => array(
                            'label'       => __( 'Sfondo area widget trasparente', 'poetheme' ),
                            'description' => __( 'Attiva per rimuovere qualsiasi colore di sfondo dal blocco widget del footer.', 'poetheme' ),
                            'type'        => 'toggle',
                            'column'      => 'left',
                        ),
                        'footer_widget_heading_h2_color' => array(
                            'label'       => __( 'Colore titolo H2', 'poetheme' ),
                            'description' => __( 'Imposta il colore dei titoli H2 dei widget del footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h2_background' => array(
                            'label'       => __( 'Sfondo titolo H2', 'poetheme' ),
                            'description' => __( 'Definisci uno sfondo, anche con trasparenza, per i titoli H2.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h3_color' => array(
                            'label'       => __( 'Colore titolo H3', 'poetheme' ),
                            'description' => __( 'Personalizza il colore dei titoli H3 presenti nei widget del footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h3_background' => array(
                            'label'       => __( 'Sfondo titolo H3', 'poetheme' ),
                            'description' => __( 'Scegli uno sfondo con supporto alla trasparenza per i titoli H3.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h4_color' => array(
                            'label'       => __( 'Colore titolo H4', 'poetheme' ),
                            'description' => __( 'Imposta il colore dei titoli H4 nei widget del footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h4_background' => array(
                            'label'       => __( 'Sfondo titolo H4', 'poetheme' ),
                            'description' => __( 'Definisci uno sfondo con trasparenza dedicato ai titoli H4.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h5_color' => array(
                            'label'       => __( 'Colore titolo H5', 'poetheme' ),
                            'description' => __( 'Personalizza il colore dei titoli H5 dei widget del footer.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                        'footer_widget_heading_h5_background' => array(
                            'label'       => __( 'Sfondo titolo H5', 'poetheme' ),
                            'description' => __( 'Scegli uno sfondo con supporto alla trasparenza per i titoli H5.', 'poetheme' ),
                            'type'        => 'color',
                            'column'      => 'right',
                        ),
                    ),
                ),
            ),
        ),
        'typography' => array(
            'title'       => __( 'Tipografia', 'poetheme' ),
            'description' => __( 'Imposta i colori delle intestazioni principali delle pagine (H1–H6).', 'poetheme' ),
            'sections'    => array(
                'headings' => array(
                    'title'  => __( 'Intestazioni (H1–H6)', 'poetheme' ),
                    'fields' => array(
                        'heading_h1_color'      => array(
                            'label'       => __( 'Colore H1', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H1.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h1_background' => array(
                            'label'       => __( 'Sfondo H1', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H1.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h2_color'      => array(
                            'label'       => __( 'Colore H2', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H2.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h2_background' => array(
                            'label'       => __( 'Sfondo H2', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H2.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h3_color'      => array(
                            'label'       => __( 'Colore H3', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H3.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h3_background' => array(
                            'label'       => __( 'Sfondo H3', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H3.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h4_color'      => array(
                            'label'       => __( 'Colore H4', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H4.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h4_background' => array(
                            'label'       => __( 'Sfondo H4', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H4.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h5_color'      => array(
                            'label'       => __( 'Colore H5', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H5.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h5_background' => array(
                            'label'       => __( 'Sfondo H5', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H5.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h6_color'      => array(
                            'label'       => __( 'Colore H6', 'poetheme' ),
                            'description' => __( 'Colore applicato alle intestazioni H6.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'heading_h6_background' => array(
                            'label'       => __( 'Sfondo H6', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per le intestazioni H6.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
                'page_titles' => array(
                    'title'       => __( 'Titoli pagine e archivi', 'poetheme' ),
                    'description' => __( 'Personalizza i colori dei titoli principali di pagine, articoli e categorie.', 'poetheme' ),
                    'fields'      => array(
                        'page_title_color'     => array(
                            'label'       => __( 'Colore titolo pagina', 'poetheme' ),
                            'description' => __( 'Colore applicato al titolo delle pagine statiche.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'page_title_background' => array(
                            'label'       => __( 'Sfondo titolo pagina', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per il titolo delle pagine statiche.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'post_title_color'     => array(
                            'label'       => __( 'Colore titolo articolo', 'poetheme' ),
                            'description' => __( 'Colore del titolo degli articoli singoli.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'post_title_background' => array(
                            'label'       => __( 'Sfondo titolo articolo', 'poetheme' ),
                            'description' => __( 'Colore di sfondo per il titolo degli articoli singoli.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'category_title_color' => array(
                            'label'       => __( 'Colore titolo categoria', 'poetheme' ),
                            'description' => __( 'Colore per i titoli delle pagine categoria e tassonomie.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                        'category_title_background' => array(
                            'label'       => __( 'Sfondo titolo categoria', 'poetheme' ),
                            'description' => __( 'Colore di sfondo dei titoli di categorie, archivi e tassonomie.', 'poetheme' ),
                            'type'        => 'color',
                        ),
                    ),
                ),
            ),
        ),
    );
}

/**
 * Configuration for the font selectors shown in the Gestione Font page.
 *
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
                'description' => __( 'Elenca i font alternativi da usare se il font principale non è disponibile.', 'poetheme' ),
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

    foreach ( $available_fonts as $font ) {
        $fonts_for_script[ $font['slug'] ] = $font['family'];
    }

    $prepared_groups = array();

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
    <div class="wrap poetheme-font-settings">
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

                                        <div class="poetheme-font-section__fields">
                                            <?php foreach ( $section['fields'] as $field_key => $field ) :
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
                                            ?>
                                                <div class="poetheme-font-section__field">
                                                    <label class="poetheme-font-section__label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                                    <div class="poetheme-font-section__control">
                                                        <select class="poetheme-font-select" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" data-preview="<?php echo esc_attr( $preview_id ); ?>"<?php echo $fallback_attr; ?> <?php disabled( empty( $available_fonts ) ); ?>>
                                                            <option value="" <?php selected( '', $value ); ?>><?php echo esc_html( $field['default_label'] ); ?></option>
                                                            <?php foreach ( $available_fonts as $font ) : ?>
                                                                <option value="<?php echo esc_attr( $font['slug'] ); ?>" <?php selected( $value, $font['slug'] ); ?> data-font-family="<?php echo esc_attr( $font['family'] ); ?>">
                                                                    <?php echo esc_html( $font['family'] ); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php if ( ! empty( $field['description'] ) ) : ?>
                                                            <p class="description"><?php echo esc_html( $field['description'] ); ?></p>
                                                        <?php endif; ?>
                                                        <div class="<?php echo esc_attr( $preview_class ); ?>" id="<?php echo esc_attr( $preview_id ); ?>" style="<?php echo esc_attr( $preview_style ); ?>">
                                                            <span class="poetheme-font-preview__label"><?php esc_html_e( 'Anteprima', 'poetheme' ); ?></span>
                                                            <span class="poetheme-font-preview__sample"><?php echo esc_html( $field['sample'] ); ?></span>
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
                                                            />
                                                            <?php if ( ! empty( $size_config['description'] ) ) : ?>
                                                                <p class="description"><?php echo esc_html( $size_config['description'] ); ?></p>
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
                                                            />
                                                            <?php if ( ! empty( $radius_config['description'] ) ) : ?>
                                                                <p class="description"><?php echo esc_html( $radius_config['description'] ); ?></p>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if ( ! empty( $field['fallback'] ) ) :
                                                            $fallback_config = $field['fallback'];
                                                        ?>
                                                            <label class="poetheme-font-fallback-label" for="<?php echo esc_attr( $fallback_id ); ?>"><?php echo esc_html( $fallback_config['label'] ); ?></label>
                                                            <input type="text" class="regular-text poetheme-font-fallback" id="<?php echo esc_attr( $fallback_id ); ?>" name="poetheme_fonts[<?php echo esc_attr( $fallback_config['key'] ); ?>]" value="<?php echo esc_attr( $fallback_value ); ?>" />
                                                            <p class="description"><?php echo esc_html( $fallback_config['description'] ); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
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
            }() );
        </script>
    </div>
    <?php
}


function poetheme_render_colors_page() {
    $options  = poetheme_get_color_options();
    $defaults = poetheme_get_default_color_options();
    $groups   = poetheme_get_color_section_groups();
    $render_color_field = static function ( $entry ) {
        $field       = $entry['field'];
        $field_id    = $entry['id'];
        $field_name  = $entry['name'];
        $type        = $entry['type'];
        $value       = $entry['value'];
        $default     = $entry['default'];
        $preview     = $entry['preview'];
        ?>
        <div class="poetheme-color-section__field">
            <label class="poetheme-color-section__label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>

            <div class="poetheme-color-section__control">
                <?php if ( 'toggle' === $type ) : ?>
                    <select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
                        <option value="0" <?php selected( false, ! empty( $value ) ); ?>><?php esc_html_e( 'No', 'poetheme' ); ?></option>
                        <option value="1" <?php selected( true, ! empty( $value ) ); ?>><?php esc_html_e( 'Sì', 'poetheme' ); ?></option>
                    </select>
                <?php else : ?>
                    <div class="poetheme-color-control">
                        <input
                            type="text"
                            class="poetheme-color-field"
                            id="<?php echo esc_attr( $field_id ); ?>"
                            name="<?php echo esc_attr( $field_name ); ?>"
                            value="<?php echo esc_attr( $value ); ?>"
                            data-default-color="<?php echo esc_attr( $default ); ?>"
                            data-supports-alpha="true"
                        />
                        <span class="poetheme-color-preview" data-preview-for="<?php echo esc_attr( $field_id ); ?>" style="--poetheme-preview-color: <?php echo esc_attr( $preview ); ?>;"></span>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $field['description'] ) ) : ?>
                    <p class="description poetheme-color-section__help"><?php echo esc_html( $field['description'] ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    };
    ?>
    <div class="wrap poetheme-color-settings">
        <h1><?php esc_html_e( 'Gestione Colori', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_colors_group' ); ?>

            <div class="poetheme-color-groups">
                <?php foreach ( $groups as $group_key => $group ) :
                    $group_classes = array( 'poetheme-color-group' );
                    $group_classes[] = 'poetheme-color-group--' . sanitize_html_class( $group_key );
                    ?>
                    <section class="<?php echo esc_attr( implode( ' ', $group_classes ) ); ?>" id="poetheme-color-group-<?php echo esc_attr( $group_key ); ?>">
                        <header class="poetheme-color-group__header">
                            <h2><?php echo esc_html( $group['title'] ); ?></h2>
                            <?php if ( ! empty( $group['description'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $group['description'] ); ?></p>
                            <?php endif; ?>
                        </header>

                        <div class="poetheme-color-group__sections">
                            <?php foreach ( $group['sections'] as $section_key => $section ) : ?>
                                <fieldset class="poetheme-color-section" id="poetheme-section-<?php echo esc_attr( $section_key ); ?>">
                                    <legend class="poetheme-color-section__title"><?php echo esc_html( $section['title'] ); ?></legend>
                                    <?php if ( ! empty( $section['description'] ) ) : ?>
                                        <p class="description poetheme-color-section__description"><?php echo esc_html( $section['description'] ); ?></p>
                                    <?php endif; ?>

                                    <?php
                                    $field_entries      = array();
                                    $has_right_column   = false;

                                    foreach ( $section['fields'] as $field_key => $field ) {
                                        $value        = isset( $options[ $field_key ] ) ? $options[ $field_key ] : '';
                                        $default      = isset( $defaults[ $field_key ] ) ? $defaults[ $field_key ] : '';
                                        $field_id     = 'poetheme-colors-' . $field_key;
                                        $field_name   = 'poetheme_colors[' . $field_key . ']';
                                        $type         = isset( $field['type'] ) ? $field['type'] : 'color';
                                        $preview_color = $value;
                                        $column       = isset( $field['column'] ) ? $field['column'] : '';

                                        if ( '' === $preview_color && '' !== $default ) {
                                            $preview_color = $default;
                                        }

                                        if ( '' === $preview_color ) {
                                            $preview_color = 'transparent';
                                        }

                                        if ( 'right' === $column ) {
                                            $has_right_column = true;
                                        }

                                        $field_entries[] = array(
                                            'field'   => $field,
                                            'id'      => $field_id,
                                            'name'    => $field_name,
                                            'type'    => $type,
                                            'value'   => $value,
                                            'default' => $default,
                                            'preview' => $preview_color,
                                            'column'  => in_array( $column, array( 'left', 'right' ), true ) ? $column : '',
                                        );
                                    }

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
                                        ?>
                                        <div class="poetheme-color-section__fields poetheme-color-section__fields--columns">
                                            <div class="poetheme-color-section__column poetheme-color-section__column--left">
                                                <?php foreach ( $left_entries as $entry ) { $render_color_field( $entry ); } ?>
                                            </div>
                                            <div class="poetheme-color-section__column poetheme-color-section__column--right">
                                                <?php foreach ( $right_entries as $entry ) { $render_color_field( $entry ); } ?>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="poetheme-color-section__fields">
                                            <?php foreach ( $field_entries as $entry ) { $render_color_field( $entry ); } ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </fieldset>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the logo settings page.
 */
function poetheme_render_logo_page() {
    $options        = poetheme_get_logo_options();
    $logo_defaults  = poetheme_get_default_logo_options();
    $logo_id         = $options['logo_id'];
    $logo            = $logo_id ? wp_get_attachment_image_src( $logo_id, 'medium' ) : false;
    $logo_height     = isset( $options['logo_height'] ) ? absint( $options['logo_height'] ) : 0;
    $show_site_title = ! empty( $options['show_site_title'] );
    $title_color     = isset( $options['title_color'] ) ? $options['title_color'] : '#111827';
    $title_size      = isset( $options['title_size'] ) ? (float) $options['title_size'] : 0;
    $site_title      = get_bloginfo( 'name' );
    $site_tagline    = get_bloginfo( 'description', 'display' );

    $logo_style_attr    = $logo_height > 0 ? ' style="height: ' . esc_attr( $logo_height ) . 'px; width: auto;"' : '';
    $title_style_attr   = '';
    $tagline_style_attr = '';

    if ( $title_color ) {
        $title_style_attr   .= 'color:' . $title_color . ';';
        $tagline_style_attr .= 'color:' . $title_color . ';opacity:0.75;';
    }

    if ( $title_size > 0 ) {
        $title_style_attr .= 'font-size:' . poetheme_format_number_for_css( $title_size ) . 'rem;';
    }

    $title_style_attr   = $title_style_attr ? ' style="' . esc_attr( $title_style_attr ) . '"' : '';
    $tagline_style_attr = $tagline_style_attr ? ' style="' . esc_attr( $tagline_style_attr ) . '"' : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Logo', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_logo_group' ); ?>
            <div id="poetheme-logo-preview" class="poetheme-logo-preview">
                <div class="poetheme-logo-preview__image-wrapper"<?php echo $show_site_title ? ' style="display:none;"' : ''; ?>>
                    <?php if ( $logo ) : ?>
                        <img src="<?php echo esc_url( $logo[0] ); ?>" alt="" class="poetheme-logo-preview__image"<?php echo $logo_style_attr; ?> />
                    <?php else : ?>
                        <p class="description"><?php esc_html_e( 'Nessun logo selezionato.', 'poetheme' ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="poetheme-logo-preview__title-wrapper"<?php echo $show_site_title ? '' : ' style="display:none;"'; ?>>
                    <div class="poetheme-logo-preview__title"<?php echo $title_style_attr; ?>><?php echo esc_html( $site_title ); ?></div>
                    <?php if ( $site_tagline ) : ?>
                        <div class="poetheme-logo-preview__tagline"<?php echo $tagline_style_attr; ?>><?php echo esc_html( $site_tagline ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <input type="hidden" name="poetheme_logo[logo_id]" id="poetheme_logo_id" value="<?php echo esc_attr( $logo_id ); ?>" />
            <p>
                <button type="button" class="button button-secondary" id="poetheme-logo-upload"><?php esc_html_e( 'Carica logo', 'poetheme' ); ?></button>
                <button type="button" class="button" id="poetheme-logo-remove" <?php disabled( 0 === $logo_id ); ?>><?php esc_html_e( 'Rimuovi logo', 'poetheme' ); ?></button>
            </p>
            <p>
                <label for="poetheme_logo_show_site_title">
                    <input type="checkbox" id="poetheme_logo_show_site_title" name="poetheme_logo[show_site_title]" value="1" <?php checked( $show_site_title ); ?> />
                    <?php esc_html_e( 'Mostra il titolo del sito al posto del logo', 'poetheme' ); ?>
                </label>
            </p>

            <div class="poetheme-logo-options"<?php echo $show_site_title ? ' style="display:none;"' : ''; ?>>
                <p>
                    <label for="poetheme_logo_height"><?php esc_html_e( 'Altezza del logo (px)', 'poetheme' ); ?></label><br />
                    <input type="number" min="0" step="1" id="poetheme_logo_height" name="poetheme_logo[logo_height]" value="<?php echo esc_attr( $logo_height ); ?>" class="small-text" />
                    <span class="description"><?php esc_html_e( 'Imposta 0 per utilizzare le proporzioni originali.', 'poetheme' ); ?></span>
                </p>
            </div>

            <div class="poetheme-title-options"<?php echo $show_site_title ? '' : ' style="display:none;"'; ?>>
                <p>
                    <label for="poetheme_logo_title_color"><?php esc_html_e( 'Colore del titolo', 'poetheme' ); ?></label><br />
                    <input
                        type="text"
                        class="poetheme-color-field"
                        id="poetheme_logo_title_color"
                        name="poetheme_logo[title_color]"
                        value="<?php echo esc_attr( $title_color ); ?>"
                        data-default-color="<?php echo esc_attr( $logo_defaults['title_color'] ); ?>"
                    />
                </p>
                <p>
                    <label for="poetheme_logo_title_size"><?php esc_html_e( 'Dimensione del titolo (rem)', 'poetheme' ); ?></label><br />
                    <input type="number" min="0.5" step="0.05" id="poetheme_logo_title_size" name="poetheme_logo[title_size]" value="<?php echo esc_attr( poetheme_format_number_for_css( $title_size ) ); ?>" class="small-text" />
                </p>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the header settings page.
 */
function poetheme_render_header_page() {
    $options       = poetheme_get_header_options();
    $layouts       = array(
        'style-1' => __( 'Layout 1 – Classico', 'poetheme' ),
        'style-2' => __( 'Layout 2 – Centrato', 'poetheme' ),
        'style-3' => __( 'Layout 3 – Minimal', 'poetheme' ),
        'style-4' => __( 'Layout 4 – Vetrina', 'poetheme' ),
        'style-5' => __( 'Layout 5 – Overlay', 'poetheme' ),
        'style-6' => __( 'Layout 6 – Sticky', 'poetheme' ),
        'style-7' => __( 'Layout 7 – Promo', 'poetheme' ),
        'style-8' => __( 'Layout 8 – E-commerce', 'poetheme' ),
    );
    $socials       = poetheme_get_header_social_networks();
    $show_cta      = ! empty( $options['show_cta'] );
    $top_bar_texts = isset( $options['top_bar_texts'] ) && is_array( $options['top_bar_texts'] ) ? $options['top_bar_texts'] : array();
    $top_bar_texts = wp_parse_args(
        $top_bar_texts,
        array(
            'text_1'  => '',
            'email'   => '',
            'phone'   => '',
            'whatsapp'=> '',
            'location_label' => '',
            'location_url'   => '',
        )
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Intestazione', 'poetheme' ); ?></h1>
        <form action="options.php" method="post" class="poetheme-options-form">
            <?php settings_fields( 'poetheme_header_group' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="poetheme_header_layout"><?php esc_html_e( 'Seleziona layout', 'poetheme' ); ?></label></th>
                        <td>
                            <select id="poetheme_header_layout" name="poetheme_header[layout]">
                                <?php foreach ( $layouts as $layout_key => $label ) : ?>
                                    <option value="<?php echo esc_attr( $layout_key ); ?>" <?php selected( $options['layout'], $layout_key ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( "Scegli quale testata applicare al tema.", 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Barra superiore', 'poetheme' ); ?></th>
                        <td>
                            <label for="poetheme_header_show_top_bar">
                                <input type="checkbox" id="poetheme_header_show_top_bar" name="poetheme_header[show_top_bar]" value="1" <?php checked( ! empty( $options['show_top_bar'] ) ); ?> />
                                <?php esc_html_e( 'Mostra la barra superiore con informazioni e social.', 'poetheme' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'La barra comprende messaggio iniziale, contatti, posizione, un menù informativo e le icone social.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="poetheme_header_top_text_info"><?php esc_html_e( 'Messaggio iniziale', 'poetheme' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="poetheme_header_top_text_info" name="poetheme_header[top_bar_texts][text_1]" value="<?php echo esc_attr( $top_bar_texts['text_1'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="poetheme_header_top_text_email"><?php esc_html_e( 'Email', 'poetheme' ); ?></label>
                        </th>
                        <td>
                            <input type="email" id="poetheme_header_top_text_email" name="poetheme_header[top_bar_texts][email]" value="<?php echo esc_attr( $top_bar_texts['email'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'esempio@dominio.it', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="poetheme_header_top_text_phone"><?php esc_html_e( 'Telefono', 'poetheme' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="poetheme_header_top_text_phone" name="poetheme_header[top_bar_texts][phone]" value="<?php echo esc_attr( $top_bar_texts['phone'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '+39 012 3456789', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="poetheme_header_top_text_whatsapp"><?php esc_html_e( 'WhatsApp', 'poetheme' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="poetheme_header_top_text_whatsapp" name="poetheme_header[top_bar_texts][whatsapp]" value="<?php echo esc_attr( $top_bar_texts['whatsapp'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '+39 012 3456789', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Call to Action', 'poetheme' ); ?></th>
                        <td>
                            <label for="poetheme_header_show_cta">
                                <input type="checkbox" id="poetheme_header_show_cta" name="poetheme_header[show_cta]" value="1" <?php checked( $show_cta ); ?> />
                                <?php esc_html_e( 'Mostra il pulsante Call to Action.', 'poetheme' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Deseleziona per nascondere il pulsante in tutte le testate.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_cta_text" class="screen-reader-text"><?php esc_html_e( 'Testo pulsante', 'poetheme' ); ?></label>
                            <input type="text" id="poetheme_header_cta_text" name="poetheme_header[cta_text]" value="<?php echo esc_attr( $options['cta_text'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Get Started', 'poetheme' ); ?>" />
                            <label for="poetheme_header_cta_url" class="screen-reader-text"><?php esc_html_e( 'Link pulsante', 'poetheme' ); ?></label>
                            <input type="url" id="poetheme_header_cta_url" name="poetheme_header[cta_url]" value="<?php echo esc_attr( $options['cta_url'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="poetheme_header_top_text_location_label"><?php esc_html_e( 'Posizione', 'poetheme' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="poetheme_header_top_text_location_label" name="poetheme_header[top_bar_texts][location_label]" value="<?php echo esc_attr( $top_bar_texts['location_label'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Es. Piazza del Duomo, Milano', 'poetheme' ); ?>" />
                            <p class="description"><?php esc_html_e( 'Testo mostrato accanto all’icona della posizione.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_top_text_location_url" class="screen-reader-text"><?php esc_html_e( 'Link Google Maps', 'poetheme' ); ?></label>
                            <input type="url" id="poetheme_header_top_text_location_url" name="poetheme_header[top_bar_texts][location_url]" value="<?php echo esc_attr( $top_bar_texts['location_url'] ); ?>" class="regular-text" placeholder="https://maps.google.com/" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Icone social', 'poetheme' ); ?></th>
                        <td>
                            <p class="description"><?php esc_html_e( 'Inserisci gli URL dei tuoi profili social per mostrarne le icone nella barra superiore.', 'poetheme' ); ?></p>
                            <?php foreach ( $socials as $key => $social ) :
                                $value = isset( $options['social_links'][ $key ] ) ? $options['social_links'][ $key ] : '';
                                ?>
                                <p>
                                    <label for="poetheme_header_social_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $social['label'] ); ?></label><br />
                                    <input type="url" id="poetheme_header_social_<?php echo esc_attr( $key ); ?>" name="poetheme_header[social_links][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="https://" />
                                </p>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(); ?>
        </form>
        <p class="description">
            <?php esc_html_e( 'Suggerimento: assegna un menù alla posizione "Top Info Menu" da Aspetto → Menu per mostrare i link informativi nella barra superiore.', 'poetheme' ); ?>
        </p>
    </div>
    <?php
}

/**
 * Render the subheader settings page.
 */
function poetheme_render_subheader_page() {
    $options      = poetheme_get_subheader_options();
    $layouts      = poetheme_get_subheader_layout_choices();
    $tags         = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
    $suggested_sep = array( '/', '>', '»', '›', '|' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Sottointestazione', 'poetheme' ); ?></h1>
        <form action="options.php" method="post" class="poetheme-options-form">
            <?php settings_fields( 'poetheme_subheader_group' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Mostra sottointestazione', 'poetheme' ); ?></th>
                        <td>
                            <label for="poetheme_subheader_enable">
                                <input type="checkbox" id="poetheme_subheader_enable" name="poetheme_subheader[enable_subheader]" value="1" <?php checked( $options['enable_subheader'] ); ?> />
                                <?php esc_html_e( 'Abilita la sezione con titolo pagina e breadcrumbs.', 'poetheme' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Se disattivata, titolo e breadcrumbs non verranno mostrati globalmente.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <fieldset id="poetheme-subheader-settings" <?php disabled( ! $options['enable_subheader'] ); ?>>
                <legend class="screen-reader-text"><?php esc_html_e( 'Impostazioni sottointestazione', 'poetheme' ); ?></legend>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Elementi visibili', 'poetheme' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="poetheme_subheader[show_title]" value="1" <?php checked( $options['show_title'] ); ?> />
                                    <?php esc_html_e( 'Mostra il titolo della pagina', 'poetheme' ); ?>
                                </label>
                                <br />
                                <label>
                                    <input type="checkbox" name="poetheme_subheader[show_breadcrumbs]" value="1" <?php checked( $options['show_breadcrumbs'] ); ?> />
                                    <?php esc_html_e( 'Mostra i breadcrumbs', 'poetheme' ); ?>
                                </label>
                                <p class="description"><?php esc_html_e( 'Le impostazioni della singola pagina possono comunque nascondere questi elementi.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="poetheme_subheader_layout"><?php esc_html_e( 'Layout', 'poetheme' ); ?></label></th>
                            <td>
                                <select id="poetheme_subheader_layout" name="poetheme_subheader[layout]">
                                    <?php foreach ( $layouts as $layout_key => $label ) : ?>
                                        <option value="<?php echo esc_attr( $layout_key ); ?>" <?php selected( $options['layout'], $layout_key ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php esc_html_e( 'Scegli come posizionare titolo e breadcrumbs nella sottointestazione.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="poetheme_subheader_title_tag"><?php esc_html_e( 'Tag del titolo', 'poetheme' ); ?></label></th>
                            <td>
                                <select id="poetheme_subheader_title_tag" name="poetheme_subheader[title_tag]">
                                    <?php foreach ( $tags as $tag ) : ?>
                                        <option value="<?php echo esc_attr( $tag ); ?>" <?php selected( $options['title_tag'], $tag ); ?>><?php echo esc_html( strtoupper( $tag ) ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php esc_html_e( 'Seleziona il tag HTML utilizzato per il titolo principale.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="poetheme_subheader_separator"><?php esc_html_e( 'Separatore breadcrumbs', 'poetheme' ); ?></label></th>
                            <td>
                                <input type="text" id="poetheme_subheader_separator" name="poetheme_subheader[breadcrumbs_separator]" value="<?php echo esc_attr( $options['breadcrumbs_separator'] ); ?>" maxlength="10" class="regular-text" list="poetheme-subheader-separators" />
                                <datalist id="poetheme-subheader-separators">
                                    <?php foreach ( $suggested_sep as $separator ) : ?>
                                        <option value="<?php echo esc_attr( $separator ); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <p class="description"><?php esc_html_e( 'Suggerimenti: /, >, », ›, | oppure qualsiasi stringa entro 10 caratteri.', 'poetheme' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function() {
            const toggle = document.getElementById('poetheme_subheader_enable');
            const fieldset = document.getElementById('poetheme-subheader-settings');

            if (!toggle || !fieldset) {
                return;
            }

            function updateState() {
                fieldset.disabled = !toggle.checked;
            }

            toggle.addEventListener('change', updateState);
            updateState();
        })();
    </script>
    <?php
}

/**
 * Render the footer settings page.
 */
function poetheme_render_footer_page() {
    $options  = poetheme_get_footer_options();
    $defaults = poetheme_get_default_footer_options();
    $choices  = poetheme_get_footer_layout_choices();

    $rows = isset( $options['rows'] ) ? (int) $options['rows'] : $defaults['rows'];
    if ( $rows < 1 || $rows > 2 ) {
        $rows = $defaults['rows'];
    }
    $display_footer = ! empty( $options['display_footer'] );
    $display_footer_credits = ! empty( $options['display_footer_credits'] );
    $credits_content = isset( $options['credits_content'] ) ? $options['credits_content'] : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Piè di pagina', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_footer_group' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Mostra il piè di pagina', 'poetheme' ); ?></th>
                        <td>
                            <label for="poetheme-footer-display">
                                <input type="checkbox" id="poetheme-footer-display" name="poetheme_footer[display_footer]" value="1" <?php checked( $display_footer ); ?> />
                                <?php esc_html_e( 'Visualizza l’intera area footer.', 'poetheme' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Disattiva per nascondere completamente i widget del piè di pagina e la sezione finale.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Credits', 'poetheme' ); ?></th>
                        <td>
                            <label for="poetheme-footer-display-credits">
                                <input type="checkbox" id="poetheme-footer-display-credits" name="poetheme_footer[display_footer_credits]" value="1" <?php checked( $display_footer_credits ); ?> />
                                <?php esc_html_e( 'Visualizza i credits.', 'poetheme' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Disattiva per nascondere la sezione con i credits nel piè di pagina.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr class="poetheme-footer-dependent">
                        <th scope="row"><label for="poetheme-footer-rows"><?php esc_html_e( 'Numero di righe', 'poetheme' ); ?></label></th>
                        <td>
                            <select id="poetheme-footer-rows" name="poetheme_footer[rows]">
                                <option value="1" <?php selected( $rows, 1 ); ?>><?php esc_html_e( '1 riga', 'poetheme' ); ?></option>
                                <option value="2" <?php selected( $rows, 2 ); ?>><?php esc_html_e( '2 righe', 'poetheme' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Scegli se visualizzare una o due righe di widget nel piè di pagina.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <?php for ( $row = 1; $row <= 2; $row++ ) :
                        $field_id   = 'poetheme-footer-layout-row-' . $row;
                        $field_name = 'poetheme_footer[row_layouts][' . $row . ']';
                        $selected   = isset( $options['row_layouts'][ $row ] ) ? $options['row_layouts'][ $row ] : $defaults['row_layouts'][ $row ];
                        if ( ! isset( $choices[ $selected ] ) ) {
                            $selected = $defaults['row_layouts'][ $row ];
                        }
                        ?>
                        <tr class="poetheme-footer-layout-row poetheme-footer-dependent" data-footer-row="<?php echo esc_attr( $row ); ?>">
                            <th scope="row"><label for="<?php echo esc_attr( $field_id ); ?>"><?php printf( esc_html__( 'Layout riga %d', 'poetheme' ), $row ); ?></label></th>
                            <td>
                                <select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
                                    <?php foreach ( $choices as $choice_key => $choice ) : ?>
                                        <option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( $selected, $choice_key ); ?>><?php echo esc_html( $choice['label'] ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php printf( esc_html__( 'Scegli quante colonne mostrare nella riga %d.', 'poetheme' ), $row ); ?></p>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    <tr class="poetheme-footer-dependent poetheme-footer-credits-dependent">
                        <th scope="row"><label for="poetheme-footer-credits-content"><?php esc_html_e( 'Testo credits', 'poetheme' ); ?></label></th>
                        <td>
                            <?php
                            wp_editor(
                                $credits_content,
                                'poetheme-footer-credits-content',
                                array(
                                    'textarea_name' => 'poetheme_footer[credits_content]',
                                    'textarea_rows' => 6,
                                    'media_buttons' => false,
                                )
                            );
                            ?>
                            <p class="description"><?php esc_html_e( 'Inserisci testo o HTML per personalizzare i credits del sito.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function() {
            const rowsSelect = document.getElementById('poetheme-footer-rows');
            const layoutRows = document.querySelectorAll('.poetheme-footer-layout-row');
            const footerToggle = document.getElementById('poetheme-footer-display');
            const footerDependentRows = document.querySelectorAll('.poetheme-footer-dependent');
            const creditsToggle = document.getElementById('poetheme-footer-display-credits');
            const creditsDependentRows = document.querySelectorAll('.poetheme-footer-credits-dependent');

            function toggleLayoutRows() {
                if (!rowsSelect || !layoutRows.length) {
                    return;
                }

                if (footerToggle && !footerToggle.checked) {
                    layoutRows.forEach(function(row) {
                        row.style.display = 'none';
                    });
                    return;
                }

                const rows = parseInt(rowsSelect.value, 10) || 1;

                layoutRows.forEach(function(row) {
                    const current = parseInt(row.getAttribute('data-footer-row'), 10) || 1;
                    row.style.display = current <= rows ? '' : 'none';
                });
            }

            function toggleCreditsRows() {
                const shouldShowFooter = !footerToggle || footerToggle.checked;
                const shouldShowCredits = !creditsToggle || creditsToggle.checked;

                creditsDependentRows.forEach(function(row) {
                    row.style.display = shouldShowFooter && shouldShowCredits ? '' : 'none';
                });
            }

            function toggleFooterRows() {
                const shouldShowFooter = !footerToggle || footerToggle.checked;

                footerDependentRows.forEach(function(row) {
                    row.style.display = shouldShowFooter ? '' : 'none';
                });

                if (shouldShowFooter) {
                    toggleLayoutRows();
                    toggleCreditsRows();
                }
            }

            if (rowsSelect) {
                rowsSelect.addEventListener('change', function() {
                    toggleLayoutRows();
                });
            }

            if (footerToggle) {
                footerToggle.addEventListener('change', function() {
                    toggleFooterRows();
                });
            }

            if (creditsToggle) {
                creditsToggle.addEventListener('change', function() {
                    toggleCreditsRows();
                });
            }

            toggleFooterRows();
            toggleCreditsRows();
        })();
    </script>
    <?php
}

/**
 * Render the custom CSS settings page.
 */
function poetheme_render_custom_css_page() {
    $custom_css = get_option( 'poetheme_custom_css', '' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Custom CSS', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_custom_css_group' ); ?>
            <label class="screen-reader-text" for="poetheme_custom_css"><?php esc_html_e( 'CSS personalizzato', 'poetheme' ); ?></label>
            <textarea id="poetheme_custom_css" name="poetheme_custom_css" rows="20" class="large-text code"><?php echo esc_textarea( $custom_css ); ?></textarea>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Retrieve logo options with defaults.
 *
 * @return array
 */
function poetheme_get_default_logo_options() {
    return array(
        'logo_id'        => 0,
        'logo_height'    => 48,
        'show_site_title'=> false,
        'title_color'    => '#111827',
        'title_size'     => 2,
    );
}

function poetheme_get_logo_options() {
    $defaults = poetheme_get_default_logo_options();
    $options  = get_option( 'poetheme_logo', array() );

    $options = wp_parse_args( $options, $defaults );

    $options['logo_id']        = absint( $options['logo_id'] );
    $options['logo_height']    = isset( $options['logo_height'] ) ? absint( $options['logo_height'] ) : $defaults['logo_height'];
    $options['show_site_title']= ! empty( $options['show_site_title'] );

    $color = isset( $options['title_color'] ) ? sanitize_hex_color( $options['title_color'] ) : '';
    $options['title_color'] = $color ? $color : $defaults['title_color'];

    $size = isset( $options['title_size'] ) ? (float) $options['title_size'] : 0;
    $options['title_size'] = $size > 0 ? $size : $defaults['title_size'];

    return $options;
}

/**
 * Sanitize logo options.
 *
 * @param array $input Raw input values.
 * @return array
 */
function poetheme_sanitize_logo_options( $input ) {
    $output = poetheme_get_logo_options();

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    if ( isset( $input['logo_id'] ) ) {
        $output['logo_id'] = absint( $input['logo_id'] );
    }

    if ( isset( $input['logo_height'] ) ) {
        $height = absint( $input['logo_height'] );
        $output['logo_height'] = $height >= 0 ? $height : $output['logo_height'];
    }

    $output['show_site_title'] = ! empty( $input['show_site_title'] );

    if ( isset( $input['title_color'] ) ) {
        $color = sanitize_hex_color( $input['title_color'] );
        if ( $color ) {
            $output['title_color'] = $color;
        }
    }

    if ( isset( $input['title_size'] ) ) {
        $size = str_replace( ',', '.', trim( (string) $input['title_size'] ) );
        $size = is_numeric( $size ) ? (float) $size : 0;

        if ( $size > 0 ) {
            $output['title_size'] = $size;
        }
    }

    return $output;
}

/**
 * Sanitize custom CSS option.
 *
 * @param string $css Raw CSS code.
 * @return string
 */
function poetheme_sanitize_custom_css( $css ) {
    if ( empty( $css ) ) {
        return '';
    }

    $css = (string) $css;

    return wp_kses( $css, array() );
}

/**
 * Sanitize header options.
 *
 * @param array $input Raw input values.
 * @return array
 */
function poetheme_sanitize_header_options( $input ) {
    $defaults = poetheme_get_default_header_options();
    $output   = $defaults;

    if ( ! is_array( $input ) ) {
        return $output;
    }

    if ( isset( $input['layout'] ) ) {
        $layout = sanitize_key( $input['layout'] );
        if ( preg_match( '/^style-[1-8]$/', $layout ) ) {
            $output['layout'] = $layout;
        }
    }

    $output['show_top_bar'] = ! empty( $input['show_top_bar'] );
    $output['show_cta']     = ! empty( $input['show_cta'] );

    $output['top_bar_texts'] = array();
    foreach ( $defaults['top_bar_texts'] as $key => $default_value ) {
        $value = '';

        if ( isset( $input['top_bar_texts'][ $key ] ) ) {
            switch ( $key ) {
                case 'email':
                    $value = sanitize_email( $input['top_bar_texts'][ $key ] );
                    break;
                case 'phone':
                case 'whatsapp':
                    $value = sanitize_text_field( $input['top_bar_texts'][ $key ] );
                    break;
                case 'location_url':
                    $value = esc_url_raw( $input['top_bar_texts'][ $key ] );
                    break;
                default:
                    $value = sanitize_text_field( $input['top_bar_texts'][ $key ] );
                    break;
            }
        }

        $output['top_bar_texts'][ $key ] = $value;
    }

    $output['cta_text'] = isset( $input['cta_text'] ) ? sanitize_text_field( $input['cta_text'] ) : '';
    $output['cta_url']  = isset( $input['cta_url'] ) ? esc_url_raw( $input['cta_url'] ) : '';

    $output['social_links'] = array();
    foreach ( poetheme_get_header_social_networks() as $key => $social ) {
        $output['social_links'][ $key ] = isset( $input['social_links'][ $key ] ) ? esc_url_raw( $input['social_links'][ $key ] ) : '';
    }

    return $output;
}

/**
 * Enqueue admin assets for the options pages.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_options_admin_assets( $hook ) {
    $style_screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-settings',
        'poetheme_page_poetheme-colors',
        'poetheme_page_poetheme-fonts',
        'poetheme_page_poetheme-logo',
        'poetheme_page_poetheme-header',
        'poetheme_page_poetheme-subheader',
        'poetheme_page_poetheme-footer',
        'poetheme_page_poetheme-custom-css',
    );

    if ( in_array( $hook, $style_screens, true ) ) {
        wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), POETHEME_VERSION );
    }

    $script_screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-settings',
        'poetheme_page_poetheme-colors',
        'poetheme_page_poetheme-logo',
    );

    if ( in_array( $hook, $script_screens, true ) ) {
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'poetheme-theme-options', POETHEME_URI . '/assets/js/theme-options.js', array( 'jquery', 'wp-color-picker' ), POETHEME_VERSION, true );
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
function poetheme_get_header_options() {
    $defaults = poetheme_get_default_header_options();
    $options  = get_option( 'poetheme_header', array() );
    $options  = wp_parse_args( $options, $defaults );

    // Ensure top bar texts always contain expected keys.
    $top_bar_defaults = $defaults['top_bar_texts'];
    $top_bar_values   = array();

    if ( isset( $options['top_bar_texts'] ) && is_array( $options['top_bar_texts'] ) ) {
        $top_bar_values = $options['top_bar_texts'];

        // Legacy support for numeric indexes.
        if ( array_values( $top_bar_values ) === $top_bar_values ) {
            $legacy_values = array_values( $top_bar_values );
            $top_bar_values = array(
                'text_1'   => isset( $legacy_values[0] ) ? $legacy_values[0] : '',
                'email'    => isset( $legacy_values[1] ) ? $legacy_values[1] : '',
                'phone'    => isset( $legacy_values[2] ) ? $legacy_values[2] : '',
                'whatsapp' => isset( $legacy_values[3] ) ? $legacy_values[3] : '',
            );
        }
    }

    $normalized_top_bar = array();
    foreach ( $top_bar_defaults as $key => $default_value ) {
        $normalized_top_bar[ $key ] = isset( $top_bar_values[ $key ] ) ? $top_bar_values[ $key ] : $default_value;
    }

    $options['top_bar_texts'] = $normalized_top_bar;

    // Merge social defaults preserving keys.
    $social_defaults = $defaults['social_links'];
    $social_values   = array();
    if ( isset( $options['social_links'] ) && is_array( $options['social_links'] ) ) {
        $social_values = $options['social_links'];
    }
    $options['social_links'] = array_merge( $social_defaults, array_intersect_key( $social_values, $social_defaults ) );

    // Validate layout fallback.
    $layout = isset( $options['layout'] ) ? sanitize_key( $options['layout'] ) : $defaults['layout'];
    if ( ! preg_match( '/^style-[1-8]$/', $layout ) ) {
        $layout = $defaults['layout'];
    }
    $options['layout'] = $layout;

    $options['show_top_bar'] = ! empty( $options['show_top_bar'] );
    $options['show_cta']     = ! empty( $options['show_cta'] );

    return $options;
}

/**
 * Register page settings meta box.
 */
function poetheme_register_page_settings_meta_box() {
    add_meta_box(
        'poetheme-page-settings',
        __( 'Impostazioni pagina', 'poetheme' ),
        'poetheme_render_page_settings_meta_box',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'poetheme_register_page_settings_meta_box' );

/**
 * Render page settings meta box content.
 *
 * @param WP_Post $post Current post object.
 */
function poetheme_render_page_settings_meta_box( $post ) {
    $defaults = poetheme_get_default_page_settings();
    $values   = get_post_meta( $post->ID, '_poetheme_page_settings', true );

    if ( ! is_array( $values ) ) {
        $values = array();
    }

    $settings = wp_parse_args( $values, $defaults );

    wp_nonce_field( 'poetheme_save_page_settings', 'poetheme_page_settings_nonce' );
    ?>
    <p>
        <label>
            <input type="checkbox" name="poetheme_page_settings[hide_breadcrumbs]" value="1" <?php checked( ! empty( $settings['hide_breadcrumbs'] ) ); ?>>
            <?php esc_html_e( 'Nascondi breadcrumbs', 'poetheme' ); ?>
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" name="poetheme_page_settings[hide_title]" value="1" <?php checked( ! empty( $settings['hide_title'] ) ); ?>>
            <?php esc_html_e( 'Nascondi titolo pagina', 'poetheme' ); ?>
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" name="poetheme_page_settings[remove_top_padding]" value="1" <?php checked( ! empty( $settings['remove_top_padding'] ) ); ?>>
            <?php esc_html_e( 'Rimuovi il padding superiore del contenuto', 'poetheme' ); ?>
        </label>
    </p>
    <p class="description"><?php esc_html_e( 'Queste impostazioni influiscono solo su questa pagina.', 'poetheme' ); ?></p>
    <?php
}

/**
 * Save page settings meta box values.
 *
 * @param int $post_id Post ID.
 */
function poetheme_save_page_settings_meta_box( $post_id ) {
    if ( ! isset( $_POST['poetheme_page_settings_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['poetheme_page_settings_nonce'] ) ), 'poetheme_save_page_settings' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $defaults = poetheme_get_default_page_settings();
    $values   = isset( $_POST['poetheme_page_settings'] ) ? wp_unslash( (array) $_POST['poetheme_page_settings'] ) : array();
    $sanitized = array();

    foreach ( $defaults as $key => $default ) {
        $sanitized[ $key ] = isset( $values[ $key ] ) && $values[ $key ] ? 1 : 0;
    }

    update_post_meta( $post_id, '_poetheme_page_settings', $sanitized );
}
add_action( 'save_post', 'poetheme_save_page_settings_meta_box' );
