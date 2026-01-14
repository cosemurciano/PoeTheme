<?php
/**
 * Dynamic output for the document <head>.
 *
 * Responsibility: render inline styles/scripts in wp_head hooks only.
 * It must NOT register settings or enqueue external assets.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Build font-related CSS rules based on theme options.
 *
 * @return string
 */
function poetheme_get_font_settings_css() {
    $font_styles = poetheme_prepare_font_styles();

    if ( empty( $font_styles['font_faces'] ) && empty( $font_styles['css_rules'] ) ) {
        return '';
    }

    return poetheme_sanitize_inline_css( $font_styles['font_faces'] . $font_styles['css_rules'] );
}

/**
 * Build layout CSS variables based on global settings.
 *
 * @return string
 */
function poetheme_get_layout_settings_css() {
    $options = poetheme_get_global_options();
    $width   = isset( $options['site_width'] ) ? absint( $options['site_width'] ) : 1200;
    $width   = max( 960, min( 1920, $width ) );

    $css = sprintf( ':root{--poetheme-site-width:%dpx;}', $width );

    return poetheme_sanitize_inline_css( $css );
}

/**
 * Build design-related CSS variables based on global settings.
 *
 * @return string
 */
function poetheme_get_design_settings_css() {
    $global_options       = poetheme_get_global_options();
    $color_options        = poetheme_get_color_options();
    $raw_color_options    = get_option( 'poetheme_colors', array() );
    $header_color_defined = is_array( $raw_color_options ) && array_key_exists( 'header_background_color', $raw_color_options );
    $styles               = '';
    $body_rule            = 'body.poetheme-has-color-settings';
    $body_css             = array();

    $page_background = ! empty( $color_options['page_background_color'] ) ? $color_options['page_background_color'] : '#f9fafb';
    $body_css[]      = 'background-color:' . esc_attr( $page_background ) . ' !important';

    $background_image_id = isset( $global_options['background_image_id'] ) ? absint( $global_options['background_image_id'] ) : 0;
    $background_position = isset( $global_options['background_position'] ) ? $global_options['background_position'] : '';
    $background_size_opt = isset( $global_options['background_size'] ) ? $global_options['background_size'] : 'auto';
    $background_url      = $background_image_id ? wp_get_attachment_image_url( $background_image_id, 'full' ) : '';

    if ( $background_url ) {
        $body_css[] = 'background-image:url(' . esc_url_raw( $background_url ) . ')';

        $parts              = array_pad( explode( ';', $background_position ), 4, '' );
        $repeat             = $parts[0];
        $position           = $parts[1];
        $attachment         = $parts[2];
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

    $footer_widget_background = ! empty( $color_options['footer_widget_background_color'] ) ? $color_options['footer_widget_background_color'] : 'transparent';

    if ( ! empty( $color_options['footer_widget_background_transparent'] ) ) {
        $footer_widget_background = 'transparent';
    }

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
        '--poetheme-page-title-color'          => ! empty( $color_options['page_title_color'] ) ? $color_options['page_title_color'] : '#111827',
        '--poetheme-page-title-background'     => ! empty( $color_options['page_title_background'] ) ? $color_options['page_title_background'] : 'transparent',
        '--poetheme-post-title-color'          => ! empty( $color_options['post_title_color'] ) ? $color_options['post_title_color'] : '#111827',
        '--poetheme-post-title-background'     => ! empty( $color_options['post_title_background'] ) ? $color_options['post_title_background'] : 'transparent',
        '--poetheme-category-title-color'      => ! empty( $color_options['category_title_color'] ) ? $color_options['category_title_color'] : '#111827',
        '--poetheme-category-title-background' => ! empty( $color_options['category_title_background'] ) ? $color_options['category_title_background'] : 'transparent',
        '--poetheme-footer-widget-heading-h2-color'      => ! empty( $color_options['footer_widget_heading_h2_color'] ) ? $color_options['footer_widget_heading_h2_color'] : 'inherit',
        '--poetheme-footer-widget-heading-h2-background' => ! empty( $color_options['footer_widget_heading_h2_background'] ) ? $color_options['footer_widget_heading_h2_background'] : 'transparent',
        '--poetheme-footer-widget-heading-h3-color'      => ! empty( $color_options['footer_widget_heading_h3_color'] ) ? $color_options['footer_widget_heading_h3_color'] : 'inherit',
        '--poetheme-footer-widget-heading-h3-background' => ! empty( $color_options['footer_widget_heading_h3_background'] ) ? $color_options['footer_widget_heading_h3_background'] : 'transparent',
        '--poetheme-footer-widget-heading-h4-color'      => ! empty( $color_options['footer_widget_heading_h4_color'] ) ? $color_options['footer_widget_heading_h4_color'] : 'inherit',
        '--poetheme-footer-widget-heading-h4-background' => ! empty( $color_options['footer_widget_heading_h4_background'] ) ? $color_options['footer_widget_heading_h4_background'] : 'transparent',
        '--poetheme-footer-widget-heading-h5-color'      => ! empty( $color_options['footer_widget_heading_h5_color'] ) ? $color_options['footer_widget_heading_h5_color'] : 'inherit',
        '--poetheme-footer-widget-heading-h5-background' => ! empty( $color_options['footer_widget_heading_h5_background'] ) ? $color_options['footer_widget_heading_h5_background'] : 'transparent',
        '--poetheme-footer-widget-text-color'            => ! empty( $color_options['footer_widget_text_color'] ) ? $color_options['footer_widget_text_color'] : 'inherit',
        '--poetheme-footer-widget-link-color'            => ! empty( $color_options['footer_widget_link_color'] ) ? $color_options['footer_widget_link_color'] : 'inherit',
        '--poetheme-footer-widget-background'            => $footer_widget_background,
    );

    $body_css[] = implode( ';', array_map( function ( $key, $value ) {
        return $key . ':' . esc_attr( $value );
    }, array_keys( $css_variables ), $css_variables ) );

    $styles .= $body_rule . '{' . implode( ';', $body_css ) . ';}';

    $styles .= 'body.poetheme-has-color-settings #primary-content .entry-content a{color:var(--poetheme-general-link-color) !important;}';

    // TODO: Consolidare i selettori ripetuti del contenuto in un asset CSS condiviso per ridurre il bloat inline.
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

    $heading_spacing_map = array(
        'heading_h1_spacing' => 'body.poetheme-has-color-settings main h1',
        'heading_h2_spacing' => 'body.poetheme-has-color-settings main h2',
        'heading_h3_spacing' => 'body.poetheme-has-color-settings main h3',
        'heading_h4_spacing' => 'body.poetheme-has-color-settings main h4',
        'heading_h5_spacing' => 'body.poetheme-has-color-settings main h5',
        'heading_h6_spacing' => 'body.poetheme-has-color-settings main h6',
    );

    foreach ( $heading_spacing_map as $spacing_key => $selector ) {
        if ( empty( $color_options[ $spacing_key ] ) ) {
            continue;
        }

        $spacing_css = poetheme_compile_spacing_css( $color_options[ $spacing_key ] );

        if ( $spacing_css ) {
            $styles .= $selector . '{' . $spacing_css . '}';
        }
    }
    $styles .= 'body.poetheme-has-color-settings .poetheme-page-title{color:var(--poetheme-page-title-color) !important;background-color:var(--poetheme-page-title-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-page-title a{color:inherit !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-post-title{color:var(--poetheme-post-title-color) !important;background-color:var(--poetheme-post-title-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-post-title a{color:inherit !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-category-title{color:var(--poetheme-category-title-color) !important;background-color:var(--poetheme-category-title-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-category-title a{color:inherit !important;}';
    // TODO: Accorpare le regole dei widget del footer in un foglio esterno per evitare duplicazioni inline.
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets{background-color:var(--poetheme-footer-widget-background) !important;color:var(--poetheme-footer-widget-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget,body.poetheme-has-color-settings .poetheme-footer-widgets .widget p,body.poetheme-has-color-settings .poetheme-footer-widgets .widget li,body.poetheme-has-color-settings .poetheme-footer-widgets .widget span{color:var(--poetheme-footer-widget-text-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget-title,body.poetheme-has-color-settings .poetheme-footer-widgets .widgettitle{color:var(--poetheme-footer-widget-heading-h2-color) !important;background-color:var(--poetheme-footer-widget-heading-h2-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h2{color:var(--poetheme-footer-widget-heading-h2-color) !important;background-color:var(--poetheme-footer-widget-heading-h2-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h3{color:var(--poetheme-footer-widget-heading-h3-color) !important;background-color:var(--poetheme-footer-widget-heading-h3-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h4{color:var(--poetheme-footer-widget-heading-h4-color) !important;background-color:var(--poetheme-footer-widget-heading-h4-background) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h5{color:var(--poetheme-footer-widget-heading-h5-color) !important;background-color:var(--poetheme-footer-widget-heading-h5-background) !important;}';

    $footer_heading_spacing_map = array(
        'footer_widget_heading_h2_spacing' => 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h2',
        'footer_widget_heading_h3_spacing' => 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h3',
        'footer_widget_heading_h4_spacing' => 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h4',
        'footer_widget_heading_h5_spacing' => 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h5',
    );

    foreach ( $footer_heading_spacing_map as $spacing_key => $selector ) {
        if ( empty( $color_options[ $spacing_key ] ) ) {
            continue;
        }

        $spacing_css = poetheme_compile_spacing_css( $color_options[ $spacing_key ] );

        if ( $spacing_css ) {
            $styles .= $selector . '{' . $spacing_css . '}';
        }
    }
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets .widget h2 a,body.poetheme-has-color-settings .poetheme-footer-widgets .widget h3 a,body.poetheme-has-color-settings .poetheme-footer-widgets .widget h4 a,body.poetheme-has-color-settings .poetheme-footer-widgets .widget h5 a,body.poetheme-has-color-settings .poetheme-footer-widgets .widget-title a,body.poetheme-has-color-settings .poetheme-footer-widgets .widgettitle a{color:inherit !important;}';
    if ( ! empty( $color_options['footer_widget_background_transparent'] ) ) {
        $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets{border-top:0 !important;}';
    }
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets a{color:var(--poetheme-footer-widget-link-color) !important;}';
    $styles .= 'body.poetheme-has-color-settings .poetheme-footer-widgets a:hover,body.poetheme-has-color-settings .poetheme-footer-widgets a:focus{color:var(--poetheme-footer-widget-link-color) !important;}';

    if ( $styles ) {
        $styles = poetheme_sanitize_inline_css( $styles );
    }

    return $styles ? $styles : '';
}

/**
 * Build custom CSS defined in the theme options.
 *
 * @return string
 */
function poetheme_get_custom_css() {
    if ( ! poetheme_user_can_manage_options() ) {
        return '';
    }

    $custom_css = get_option( 'poetheme_custom_css', '' );

    if ( empty( $custom_css ) ) {
        return '';
    }

    $custom_css = poetheme_sanitize_inline_css( $custom_css );

    if ( '' === $custom_css ) {
        return '';
    }

    return $custom_css;
}

/**
 * Build the final inline CSS output for the theme.
 *
 * @return array
 */
function poetheme_build_inline_css() {
    $css_blocks = array(
        'core'   => poetheme_get_font_settings_css() . poetheme_get_layout_settings_css(),
        'design' => poetheme_get_design_settings_css(),
        'custom' => poetheme_get_custom_css(),
    );

    foreach ( $css_blocks as $key => $value ) {
        $css_blocks[ $key ] = $value ? $value : '';
    }

    $max_bytes   = 20 * 1024;
    $combined    = implode( '', $css_blocks );
    $total_bytes = strlen( $combined );

    if ( $total_bytes > $max_bytes ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log(
                sprintf(
                    'PoeTheme inline CSS exceeded %d bytes (%d bytes). Custom CSS excluded.',
                    $max_bytes,
                    $total_bytes
                )
            );
        }

        $css_blocks['custom'] = '';
        $combined             = $css_blocks['core'] . $css_blocks['design'];
    }

    return array(
        'css'    => $combined,
        'blocks' => $css_blocks,
    );
}

/**
 * Output inline CSS in a single, traceable style tag.
 */
function poetheme_output_inline_css() {
    $result = poetheme_build_inline_css();
    $css    = $result['css'];

    if ( '' === $css ) {
        return;
    }

    $output = '';

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $sizes = array(
            'core'   => strlen( $result['blocks']['core'] ),
            'design' => strlen( $result['blocks']['design'] ),
            'custom' => strlen( $result['blocks']['custom'] ),
        );

        $output .= "/* PoeTheme Inline CSS\n";
        $output .= sprintf( " * core:   %s KB\n", poetheme_format_inline_css_size( $sizes['core'] ) );
        $output .= sprintf( " * design: %s KB\n", poetheme_format_inline_css_size( $sizes['design'] ) );
        $output .= sprintf( " * custom: %s KB\n", poetheme_format_inline_css_size( $sizes['custom'] ) );
        $output .= " */\n";
    }

    $output .= $css;

    printf( '<style id="poetheme-inline-css">%s</style>', esc_html( $output ) );
}
add_action( 'wp_head', 'poetheme_output_inline_css', 95 );

/**
 * Format a byte count to kilobytes for debug output.
 *
 * @param int $bytes Bytes count.
 * @return string
 */
function poetheme_format_inline_css_size( $bytes ) {
    $bytes = max( 0, (int) $bytes );
    $kb    = $bytes / 1024;

    return number_format( $kb, 2 );
}

if ( function_exists( 'poetheme_schema_output_jsonld' ) ) {
    add_action( 'wp_head', 'poetheme_schema_output_jsonld', 20 );
}
