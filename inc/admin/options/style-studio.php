<?php
/**
 * Style Studio (Phase 1: color harmony engine).
 *
 * A client-side generator that derives a full, harmonious color set from a few
 * seeds (brand color, harmony rule, light/dark mode). The result is saved as a
 * style palette (see palettes.php) and can be applied like any other palette.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Allowed harmony rules.
 *
 * @return string[]
 */
function poetheme_get_studio_harmonies() {
    return array( 'complementary', 'analogous', 'triadic', 'split', 'monochromatic' );
}

/**
 * Register the Style Studio submenu.
 */
function poetheme_studio_admin_menu() {
    add_submenu_page(
        'poetheme-settings',
        __( 'Style Studio', 'poetheme' ),
        __( 'Style Studio', 'poetheme' ),
        'manage_options',
        'poetheme-style-studio',
        'poetheme_render_style_studio_page'
    );
}
add_action( 'admin_menu', 'poetheme_studio_admin_menu', 11 );

/**
 * Enqueue Style Studio assets.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_studio_admin_assets( $hook ) {
    if ( 'poetheme_page_poetheme-style-studio' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), poetheme_get_asset_version( 'assets/css/theme-options.css' ) );

    // Register a unique @font-face per available font so the live preview can
    // render the actually selected typeface (browsers fetch lazily on use).
    $faces = '';
    foreach ( poetheme_get_available_fonts() as $slug => $font ) {
        if ( empty( $font['url'] ) ) {
            continue;
        }
        $faces .= sprintf(
            "@font-face{font-family:'poetheme-pv-%s';src:url('%s') format('%s');font-display:swap;}\n",
            $slug,
            $font['url'],
            $font['format']
        );
    }
    if ( '' !== $faces ) {
        wp_add_inline_style( 'poetheme-theme-options', $faces );
    }

    wp_enqueue_script( 'poetheme-style-studio', POETHEME_URI . '/assets/js/style-studio.js', array(), poetheme_get_asset_version( 'assets/js/style-studio.js' ), true );

    // When editing an existing palette, pass its seeds so the controls preload.
    $edit_seeds     = null;
    $edit_name      = '';
    $edit_overrides = null;
    $edit_id        = isset( $_GET['palette'] ) ? sanitize_text_field( wp_unslash( $_GET['palette'] ) ) : '';
    if ( '' !== $edit_id ) {
        $palettes = poetheme_get_style_palettes();
        if ( isset( $palettes[ $edit_id ] ) && ! empty( $palettes[ $edit_id ]['seeds'] ) ) {
            $edit_seeds     = poetheme_sanitize_studio_seeds( $palettes[ $edit_id ]['seeds'] );
            $edit_name      = isset( $palettes[ $edit_id ]['name'] ) ? $palettes[ $edit_id ]['name'] : '';
            $edit_overrides = isset( $palettes[ $edit_id ]['overrides'] ) ? $palettes[ $edit_id ]['overrides'] : null;
        } else {
            $edit_id = '';
        }
    }

    wp_localize_script(
        'poetheme-style-studio',
        'poethemeStudio',
        array(
            'editSeeds'     => $edit_seeds,
            'editId'        => $edit_id,
            'editName'      => $edit_name,
            'editOverrides' => $edit_overrides,
            'editingLabel' => $edit_name ? sprintf(
                /* translators: %s: palette name */
                __( 'Stai modificando: %s', 'poetheme' ),
                $edit_name
            ) : '',
            'labels' => array(
                'aaPass'       => __( 'AA', 'poetheme' ),
                'aaFail'       => __( 'Insufficiente', 'poetheme' ),
                'themeDefault' => __( 'Predefinito del tema', 'poetheme' ),
                'menu'         => array( __( 'Home', 'poetheme' ), __( 'Articoli', 'poetheme' ), __( 'Contatti', 'poetheme' ) ),
                'sampleText'   => array(
                    'brand'         => __( 'Brand', 'poetheme' ),
                    'subscribe'     => __( 'Iscriviti', 'poetheme' ),
                    'title'         => __( 'Titolo della pagina di esempio', 'poetheme' ),
                    'meta'          => __( 'Di Redazione · 12 giugno 2026 · 5 min di lettura', 'poetheme' ),
                    'lead'          => __( 'Un paragrafo introduttivo con un ', 'poetheme' ),
                    'link'          => __( 'collegamento', 'poetheme' ),
                    'leadEnd'       => __( ' e del testo in ', 'poetheme' ),
                    'bold'          => __( 'grassetto', 'poetheme' ),
                    'h2'            => __( 'Una sezione importante', 'poetheme' ),
                    'body'          => __( 'Testo di esempio con del codice ', 'poetheme' ),
                    'list'          => array( __( 'Primo punto elenco', 'poetheme' ), __( 'Secondo punto elenco', 'poetheme' ), __( 'Terzo punto elenco', 'poetheme' ) ),
                    'quote'         => __( '“Una citazione che mette in risalto un concetto chiave del contenuto.”', 'poetheme' ),
                    'image'         => __( 'Immagine in evidenza', 'poetheme' ),
                    'caption'       => __( 'Didascalia dell’immagine di esempio.', 'poetheme' ),
                    'h3'            => __( 'Dettagli e dati', 'poetheme' ),
                    'steps'         => array( __( 'Primo passaggio', 'poetheme' ), __( 'Secondo passaggio', 'poetheme' ), __( 'Terzo passaggio', 'poetheme' ) ),
                    'tableHead'     => array( __( 'Piano', 'poetheme' ), __( 'Prezzo', 'poetheme' ) ),
                    'tableRows'     => array( array( __( 'Base', 'poetheme' ), '€9' ), array( __( 'Pro', 'poetheme' ), '€29' ) ),
                    'cta'           => __( 'Azione principale', 'poetheme' ),
                    'secondary'     => __( 'Secondaria', 'poetheme' ),
                    'footerHeading' => __( 'Newsletter', 'poetheme' ),
                ),
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'poetheme_studio_admin_assets' );

/**
 * Sanitize the seeds payload from the Style Studio.
 *
 * @param array $seeds Raw seeds.
 * @return array
 */
function poetheme_sanitize_studio_seeds( $seeds ) {
    $seeds = is_array( $seeds ) ? $seeds : array();

    $base = isset( $seeds['base'] ) ? sanitize_hex_color( (string) $seeds['base'] ) : '';
    if ( ! $base ) {
        $base = '#2563eb';
    }

    $harmony = isset( $seeds['harmony'] ) ? sanitize_key( $seeds['harmony'] ) : 'complementary';
    if ( ! in_array( $harmony, poetheme_get_studio_harmonies(), true ) ) {
        $harmony = 'complementary';
    }

    $mode = isset( $seeds['mode'] ) && 'dark' === $seeds['mode'] ? 'dark' : 'light';

    $ratios = poetheme_get_studio_ratios();
    $ratio  = isset( $seeds['ratio'] ) ? (string) $seeds['ratio'] : '1.25';
    if ( ! isset( $ratios[ $ratio ] ) ) {
        $ratio = '1.25';
    }

    $base_size = isset( $seeds['base_size'] ) ? (float) $seeds['base_size'] : 1.0;
    $base_size = max( 0.8, min( 1.5, $base_size ) );

    $density = isset( $seeds['density'] ) ? sanitize_key( $seeds['density'] ) : 'comfortable';
    if ( ! in_array( $density, array( 'compact', 'comfortable', 'spacious' ), true ) ) {
        $density = 'comfortable';
    }

    $radius = isset( $seeds['radius'] ) ? (float) $seeds['radius'] : 8.0;
    $radius = max( 0.0, min( 999.0, $radius ) );

    return array(
        'base'           => $base,
        'harmony'        => $harmony,
        'mode'           => $mode,
        'accent_buttons' => ! empty( $seeds['accent_buttons'] ),
        'heading_font'   => isset( $seeds['heading_font'] ) ? sanitize_text_field( (string) $seeds['heading_font'] ) : '',
        'body_font'      => isset( $seeds['body_font'] ) ? sanitize_text_field( (string) $seeds['body_font'] ) : '',
        'base_size'      => $base_size,
        'ratio'          => $ratio,
        'density'        => $density,
        'radius'         => $radius,
    );
}

/**
 * Handle saving a generated palette from the Style Studio.
 */
function poetheme_handle_style_studio_save() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    check_admin_referer( 'poetheme_style_studio_save' );

    $raw     = isset( $_POST['poetheme_studio_payload'] ) ? wp_unslash( $_POST['poetheme_studio_payload'] ) : '';
    $payload = json_decode( (string) $raw, true );

    $redirect = admin_url( 'admin.php?page=poetheme-style-studio' );

    if ( ! is_array( $payload ) ) {
        wp_safe_redirect( add_query_arg( 'poetheme_palette_notice', 'import_invalid', $redirect ) );
        exit;
    }

    $name = isset( $payload['name'] ) ? sanitize_text_field( wp_strip_all_tags( (string) $payload['name'] ) ) : '';
    if ( '' === $name ) {
        $name = __( 'Palette generata', 'poetheme' );
    }

    // Seeds are authoritative: the full palette is generated server-side so the
    // saved tokens are canonical (and editable later from these seeds).
    $seeds     = isset( $payload['seeds'] ) ? poetheme_sanitize_studio_seeds( $payload['seeds'] ) : array();
    $overrides = isset( $payload['overrides'] ) && is_array( $payload['overrides'] ) ? $payload['overrides'] : array();

    $palettes = poetheme_get_style_palettes();

    // Update an existing palette when an id is provided, otherwise create one.
    $edit_id = isset( $payload['palette_id'] ) ? sanitize_text_field( (string) $payload['palette_id'] ) : '';
    if ( '' !== $edit_id && isset( $palettes[ $edit_id ] ) ) {
        $id     = $edit_id;
        $origin = isset( $palettes[ $id ]['origin'] ) ? $palettes[ $id ]['origin'] : 'studio';
    } else {
        $id     = 'pal_' . wp_generate_password( 10, false, false );
        while ( isset( $palettes[ $id ] ) ) {
            $id = 'pal_' . wp_generate_password( 10, false, false );
        }
        $origin = 'studio';
    }

    $palette          = poetheme_studio_build_palette( $name, $seeds, $origin, $overrides );
    $palette['updated'] = time();
    if ( isset( $palettes[ $id ]['created'] ) ) {
        $palette['created'] = $palettes[ $id ]['created'];
    }

    $palettes[ $id ] = $palette;
    update_option( 'poetheme_style_palettes', $palettes );

    $apply = ! empty( $_POST['apply'] );
    if ( $apply ) {
        update_option( 'poetheme_active_palette', $id );
    }

    $is_update = ( '' !== $edit_id && $edit_id === $id );
    $notice    = $apply ? 'activated' : ( $is_update ? 'updated' : 'imported' );

    $target = admin_url( 'admin.php?page=poetheme-palette' );
    wp_safe_redirect( add_query_arg( 'poetheme_palette_notice', $notice, $target ) );
    exit;
}
add_action( 'admin_post_poetheme_style_studio_save', 'poetheme_handle_style_studio_save' );

/**
 * Allowed modular-scale ratios (label => factor).
 *
 * @return array<string,float>
 */
function poetheme_get_studio_ratios() {
    return array(
        '1.125' => 1.125,
        '1.2'   => 1.2,
        '1.25'  => 1.25,
        '1.333' => 1.333,
        '1.414' => 1.414,
    );
}

/* -------------------------------------------------------------------------
 * Server-side generator (canonical source of truth, mirrors style-studio.js).
 * Lets the theme build full palettes from seeds without a browser (e.g. when
 * seeding the default presets on activation).
 * ---------------------------------------------------------------------- */

/**
 * Clamp a number between a min and max.
 *
 * @param float $value Value.
 * @param float $min   Minimum.
 * @param float $max   Maximum.
 * @return float
 */
function poetheme_studio_clamp( $value, $min, $max ) {
    return max( $min, min( $max, $value ) );
}

/**
 * Convert a hex color to an [h, s, l] array (deg, %, %).
 *
 * @param string $hex Hex color.
 * @return array{0:float,1:float,2:float}
 */
function poetheme_studio_hex_to_hsl( $hex ) {
    $hex = ltrim( (string) $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
        $hex = '2563eb';
    }

    $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
    $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
    $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

    $max = max( $r, $g, $b );
    $min = min( $r, $g, $b );
    $h   = 0;
    $s   = 0;
    $l   = ( $max + $min ) / 2;

    if ( $max !== $min ) {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
        if ( $max === $r ) {
            $h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
        } elseif ( $max === $g ) {
            $h = ( $b - $r ) / $d + 2;
        } else {
            $h = ( $r - $g ) / $d + 4;
        }
        $h /= 6;
    }

    return array( $h * 360, $s * 100, $l * 100 );
}

/**
 * Convert HSL (deg, %, %) to a hex color.
 *
 * @param float $h Hue.
 * @param float $s Saturation.
 * @param float $l Lightness.
 * @return string
 */
function poetheme_studio_hsl_to_hex( $h, $s, $l ) {
    $h = fmod( fmod( $h, 360 ) + 360, 360 ) / 360;
    $s = poetheme_studio_clamp( $s, 0, 100 ) / 100;
    $l = poetheme_studio_clamp( $l, 0, 100 ) / 100;

    if ( 0 === (int) ( $s * 1000 ) && 0.0 === (float) $s ) {
        $r = $g = $b = $l;
    } else {
        $hue = function ( $p, $q, $t ) {
            if ( $t < 0 ) {
                $t += 1;
            }
            if ( $t > 1 ) {
                $t -= 1;
            }
            if ( $t < 1 / 6 ) {
                return $p + ( $q - $p ) * 6 * $t;
            }
            if ( $t < 1 / 2 ) {
                return $q;
            }
            if ( $t < 2 / 3 ) {
                return $p + ( $q - $p ) * ( 2 / 3 - $t ) * 6;
            }
            return $p;
        };
        $q = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        $r = $hue( $p, $q, $h + 1 / 3 );
        $g = $hue( $p, $q, $h );
        $b = $hue( $p, $q, $h - 1 / 3 );
    }

    $to_hex = function ( $v ) {
        $v = (int) round( poetheme_studio_clamp( $v * 255, 0, 255 ) );
        return str_pad( dechex( $v ), 2, '0', STR_PAD_LEFT );
    };

    return '#' . $to_hex( $r ) . $to_hex( $g ) . $to_hex( $b );
}

/**
 * Relative luminance of a hex color.
 *
 * @param string $hex Hex color.
 * @return float
 */
function poetheme_studio_luminance( $hex ) {
    $hex = ltrim( (string) $hex, '#' );
    if ( 6 !== strlen( $hex ) ) {
        $hex = '000000';
    }
    $channels = array(
        hexdec( substr( $hex, 0, 2 ) ) / 255,
        hexdec( substr( $hex, 2, 2 ) ) / 255,
        hexdec( substr( $hex, 4, 2 ) ) / 255,
    );
    foreach ( $channels as $i => $v ) {
        $channels[ $i ] = $v <= 0.03928 ? $v / 12.92 : pow( ( $v + 0.055 ) / 1.055, 2.4 );
    }
    return $channels[0] * 0.2126 + $channels[1] * 0.7152 + $channels[2] * 0.0722;
}

/**
 * Pick black or white for best contrast on a background.
 *
 * @param string $bg Background hex.
 * @return string
 */
function poetheme_studio_best_on( $bg ) {
    $l  = poetheme_studio_luminance( $bg );
    $cw = ( max( $l, poetheme_studio_luminance( '#ffffff' ) ) + 0.05 ) / ( min( $l, poetheme_studio_luminance( '#ffffff' ) ) + 0.05 );
    $cd = ( max( $l, poetheme_studio_luminance( '#111827' ) ) + 0.05 ) / ( min( $l, poetheme_studio_luminance( '#111827' ) ) + 0.05 );
    return $cw >= $cd ? '#ffffff' : '#111827';
}

/**
 * Accent hue for a harmony rule.
 *
 * @param float  $h       Base hue.
 * @param string $harmony Harmony rule.
 * @return float
 */
function poetheme_studio_accent_hue( $h, $harmony ) {
    switch ( $harmony ) {
        case 'analogous':
            return $h + 30;
        case 'triadic':
            return $h + 120;
        case 'split':
            return $h + 150;
        case 'monochromatic':
            return $h;
        default:
            return $h + 180;
    }
}

/**
 * Round to two decimals.
 *
 * @param float $n Number.
 * @return float
 */
function poetheme_studio_round2( $n ) {
    return round( $n * 100 ) / 100;
}

/**
 * Generate a full palette (colors + fonts + global) from sanitized seeds.
 *
 * @param array $seeds Sanitized seeds (see poetheme_sanitize_studio_seeds()).
 * @return array{colors:array,fonts:array,global:array}
 */
function poetheme_studio_generate_from_seeds( $seeds ) {
    $seeds = poetheme_sanitize_studio_seeds( $seeds );

    list( $bh, $bs, $bl ) = poetheme_studio_hex_to_hsl( $seeds['base'] );
    $h    = $bh;
    $s    = poetheme_studio_clamp( $bs, 25, 90 );
    $a_h  = poetheme_studio_accent_hue( $h, $seeds['harmony'] );
    $dark = 'dark' === $seeds['mode'];

    $primary = poetheme_studio_hsl_to_hex( $h, $s, poetheme_studio_clamp( $bl, 38, 56 ) );
    $accent  = poetheme_studio_hsl_to_hex( $a_h, $s, poetheme_studio_clamp( $bl, 40, 58 ) );
    $cta_bg  = $seeds['accent_buttons'] ? $accent : $primary;

    if ( $dark ) {
        $page        = poetheme_studio_hsl_to_hex( $h, 18, 10 );
        $surface     = poetheme_studio_hsl_to_hex( $h, 16, 14 );
        $header_bg   = poetheme_studio_hsl_to_hex( $h, 16, 13 );
        $footer_bg   = poetheme_studio_hsl_to_hex( $h, 18, 9 );
        $top_bar     = poetheme_studio_hsl_to_hex( $h, 22, 7 );
        $text        = poetheme_studio_hsl_to_hex( $h, 14, 92 );
        $text_strong = poetheme_studio_hsl_to_hex( $h, 16, 97 );
        $text_muted  = poetheme_studio_hsl_to_hex( $h, 12, 70 );
        $menu_link   = $text_muted;
        $link        = poetheme_studio_hsl_to_hex( $h, $s, 70 );
    } else {
        $page        = poetheme_studio_hsl_to_hex( $h, 14, 98 );
        $surface     = '#ffffff';
        $header_bg   = '#ffffff';
        $footer_bg   = poetheme_studio_hsl_to_hex( $h, 14, 96 );
        $top_bar     = poetheme_studio_hsl_to_hex( $h, 24, 12 );
        $text        = poetheme_studio_hsl_to_hex( $h, 16, 14 );
        $text_strong = poetheme_studio_hsl_to_hex( $h, 22, 9 );
        $text_muted  = poetheme_studio_hsl_to_hex( $h, 10, 42 );
        $menu_link   = poetheme_studio_hsl_to_hex( $h, 12, 32 );
        $link        = $primary;
    }

    $on_dark = '#ffffff';

    $colors = array(
        'page_background_color'         => $page,
        'content_background_color'      => $surface,
        'content_text_color'            => $text,
        'content_strong_color'          => $text_strong,
        'content_link_color'            => $link,
        'content_link_underline'        => false,
        'general_link_color'            => $link,
        'header_background_color'       => $header_bg,
        'menu_link_color'               => $menu_link,
        'menu_active_link_color'        => $link,
        'cta_background_color'          => $cta_bg,
        'cta_text_color'                => poetheme_studio_best_on( $cta_bg ),
        'top_bar_background_color'      => $top_bar,
        'top_bar_text_color'            => $on_dark,
        'top_bar_link_color'            => $on_dark,
        'top_bar_icon_color'            => $on_dark,
        'page_title_color'              => $text_strong,
        'post_title_color'              => $text_strong,
        'category_title_color'          => $text_strong,
        'footer_widget_background_color'=> $footer_bg,
        'footer_widget_text_color'      => $text_muted,
        'footer_widget_link_color'      => $link,
    );

    foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $tag ) {
        $colors[ 'heading_' . $tag . '_color' ] = $text_strong;
    }
    foreach ( array( 'h2', 'h3', 'h4', 'h5' ) as $tag ) {
        $colors[ 'footer_widget_heading_' . $tag . '_color' ] = $text_strong;
    }

    // Typography + density.
    $ratios   = poetheme_get_studio_ratios();
    $ratio    = isset( $ratios[ $seeds['ratio'] ] ) ? $ratios[ $seeds['ratio'] ] : 1.25;
    $base     = $seeds['base_size'];
    $density  = array(
        'compact'     => array( 'spacing' => 0.4, 'width' => 1120 ),
        'comfortable' => array( 'spacing' => 0.75, 'width' => 1280 ),
        'spacious'    => array( 'spacing' => 1.1, 'width' => 1440 ),
    );
    $d        = isset( $density[ $seeds['density'] ] ) ? $density[ $seeds['density'] ] : $density['comfortable'];
    $exp      = array( 'h1' => 5, 'h2' => 4, 'h3' => 3, 'h4' => 2, 'h5' => 1.4, 'h6' => 0.8 );
    $sizes    = array();
    foreach ( $exp as $tag => $e ) {
        $sizes[ $tag ] = poetheme_studio_round2( $base * pow( $ratio, $e ) );
    }

    $fonts = array(
        'heading_font'                       => $seeds['heading_font'],
        'body_font'                          => $seeds['body_font'],
        'body_font_size'                     => poetheme_studio_round2( $base ),
        'heading_font_size'                  => $sizes['h1'],
        'heading_h2_font_size'               => $sizes['h2'],
        'heading_h3_font_size'               => $sizes['h3'],
        'heading_h4_font_size'               => $sizes['h4'],
        'heading_h5_font_size'               => $sizes['h5'],
        'heading_h6_font_size'               => $sizes['h6'],
        'page_title_font_size'               => $sizes['h1'],
        'post_title_font_size'               => $sizes['h1'],
        'category_title_font_size'           => $sizes['h2'],
        'footer_widget_heading_font_size'    => $sizes['h3'],
        'footer_widget_heading_h2_font_size' => poetheme_studio_round2( $sizes['h2'] * 0.85 ),
        'footer_widget_heading_h3_font_size' => poetheme_studio_round2( $sizes['h3'] * 0.85 ),
        'footer_widget_heading_h4_font_size' => poetheme_studio_round2( $sizes['h4'] * 0.9 ),
        'footer_widget_heading_h5_font_size' => poetheme_studio_round2( $sizes['h5'] * 0.9 ),
        'footer_widget_text_font_size'       => poetheme_studio_round2( $base ),
        'top_bar_text_font_size'             => poetheme_studio_round2( $base * 0.9 ),
        'cta_text_font_size'                 => poetheme_studio_round2( $base ),
        'cta_button_border_radius'           => $seeds['radius'],
    );

    $spacing = array(
        'margin'  => array( 'top' => '0', 'right' => '', 'bottom' => $d['spacing'] . 'rem', 'left' => '' ),
        'padding' => array( 'top' => '', 'right' => '', 'bottom' => '', 'left' => '' ),
    );
    foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $tag ) {
        $fonts[ 'heading_' . $tag . '_spacing' ] = $spacing;
    }

    $global = array(
        'layout_mode' => 'full',
        'site_width'  => $d['width'],
    );

    return array(
        'colors' => $colors,
        'fonts'  => $fonts,
        'global' => $global,
    );
}

/* -------------------------------------------------------------------------
 * Built-in presets + default seeding on theme activation.
 * ---------------------------------------------------------------------- */

/**
 * Built-in preset definitions (seed sets). Font preferences are resolved to
 * available font slugs at generation time.
 *
 * @return array[]
 */
function poetheme_get_studio_presets() {
    return array(
        array(
            'name'           => __( 'Aziendale', 'poetheme' ),
            'base'           => '#2563eb',
            'harmony'        => 'analogous',
            'mode'           => 'light',
            'accent_buttons' => false,
            'base_size'      => 1.0,
            'ratio'          => '1.25',
            'density'        => 'comfortable',
            'radius'         => 8,
            'heading_pref'   => array( 'inter' ),
            'body_pref'      => array( 'inter', 'roboto' ),
        ),
        array(
            'name'           => __( 'Editoriale', 'poetheme' ),
            'base'           => '#b91c1c',
            'harmony'        => 'complementary',
            'mode'           => 'light',
            'accent_buttons' => false,
            'base_size'      => 1.05,
            'ratio'          => '1.333',
            'density'        => 'comfortable',
            'radius'         => 0,
            'heading_pref'   => array( 'playfair' ),
            'body_pref'      => array( 'roboto', 'inter' ),
        ),
        array(
            'name'           => __( 'Boutique', 'poetheme' ),
            'base'           => '#d97706',
            'harmony'        => 'analogous',
            'mode'           => 'light',
            'accent_buttons' => true,
            'base_size'      => 1.0,
            'ratio'          => '1.25',
            'density'        => 'spacious',
            'radius'         => 999,
            'heading_pref'   => array( 'playfair' ),
            'body_pref'      => array( 'inter' ),
        ),
        array(
            'name'           => __( 'Notturno', 'poetheme' ),
            'base'           => '#6366f1',
            'harmony'        => 'complementary',
            'mode'           => 'dark',
            'accent_buttons' => false,
            'base_size'      => 1.0,
            'ratio'          => '1.25',
            'density'        => 'comfortable',
            'radius'         => 8,
            'heading_pref'   => array( 'inter' ),
            'body_pref'      => array( 'inter', 'roboto' ),
        ),
        array(
            'name'           => __( 'Tech', 'poetheme' ),
            'base'           => '#06b6d4',
            'harmony'        => 'triadic',
            'mode'           => 'light',
            'accent_buttons' => false,
            'base_size'      => 1.0,
            'ratio'          => '1.2',
            'density'        => 'compact',
            'radius'         => 8,
            'heading_pref'   => array( 'inter', 'bebas' ),
            'body_pref'      => array( 'inter', 'roboto' ),
        ),
        array(
            'name'           => __( 'Natura', 'poetheme' ),
            'base'           => '#16a34a',
            'harmony'        => 'analogous',
            'mode'           => 'light',
            'accent_buttons' => false,
            'base_size'      => 1.0,
            'ratio'          => '1.25',
            'density'        => 'comfortable',
            'radius'         => 999,
            'heading_pref'   => array( 'playfair', 'inter' ),
            'body_pref'      => array( 'inter', 'roboto' ),
        ),
    );
}

/**
 * Resolve a list of font-name preferences to an available font slug.
 *
 * @param array $prefs Substrings to look for (e.g. array( 'playfair' )).
 * @return string Slug or empty string.
 */
function poetheme_studio_resolve_font_pref( $prefs ) {
    if ( empty( $prefs ) || ! function_exists( 'poetheme_get_available_fonts' ) ) {
        return '';
    }

    $available = poetheme_get_available_fonts();

    foreach ( (array) $prefs as $pref ) {
        $needle = strtolower( (string) $pref );
        foreach ( $available as $slug => $font ) {
            $hay = strtolower( $slug . ' ' . ( isset( $font['family'] ) ? $font['family'] : '' ) );
            if ( false !== strpos( $hay, $needle ) ) {
                return $slug;
            }
        }
    }

    return '';
}

/**
 * Convert a preset definition into a sanitized seeds array.
 *
 * @param array $preset Preset definition.
 * @return array
 */
function poetheme_studio_preset_to_seeds( $preset ) {
    return poetheme_sanitize_studio_seeds(
        array(
            'base'           => $preset['base'],
            'harmony'        => $preset['harmony'],
            'mode'           => $preset['mode'],
            'accent_buttons' => ! empty( $preset['accent_buttons'] ),
            'base_size'      => $preset['base_size'],
            'ratio'          => $preset['ratio'],
            'density'        => $preset['density'],
            'radius'         => $preset['radius'],
            'heading_font'   => poetheme_studio_resolve_font_pref( isset( $preset['heading_pref'] ) ? $preset['heading_pref'] : array() ),
            'body_font'      => poetheme_studio_resolve_font_pref( isset( $preset['body_pref'] ) ? $preset['body_pref'] : array() ),
        )
    );
}

/**
 * Build a stored palette array from sanitized seeds (+ optional advanced overrides).
 *
 * @param string $name      Palette name.
 * @param array  $seeds     Sanitized seeds.
 * @param string $origin    'preset' or 'studio'.
 * @param array  $overrides Optional manual token overrides: array( 'colors' => [], 'fonts' => [] ).
 * @return array
 */
function poetheme_studio_build_palette( $name, $seeds, $origin = 'studio', $overrides = array() ) {
    $generated = poetheme_studio_generate_from_seeds( $seeds );

    $ov_colors = isset( $overrides['colors'] ) && is_array( $overrides['colors'] ) ? $overrides['colors'] : array();
    $ov_fonts  = isset( $overrides['fonts'] ) && is_array( $overrides['fonts'] ) ? $overrides['fonts'] : array();
    $ov_global = isset( $overrides['global'] ) && is_array( $overrides['global'] ) ? $overrides['global'] : array();

    // Generated tokens first, then manual overrides on top, then sanitize.
    $colors = poetheme_sanitize_color_options( array_merge( $generated['colors'], $ov_colors ) );
    $fonts  = poetheme_sanitize_font_options( array_merge( $generated['fonts'], $ov_fonts ) );
    $global = poetheme_sanitize_palette_global( array_merge( $generated['global'], $ov_global ) );

    // Store only the overridden keys (sanitized) so they remain editable.
    $stored_overrides = array(
        'colors' => array_intersect_key( $colors, $ov_colors ),
        'fonts'  => array_intersect_key( $fonts, $ov_fonts ),
        'global' => array_intersect_key( $global, $ov_global ),
    );

    return array(
        'name'      => $name,
        'colors'    => $colors,
        'fonts'     => $fonts,
        'global'    => $global,
        'seeds'     => $seeds,
        'overrides' => $stored_overrides,
        'origin'    => 'preset' === $origin ? 'preset' : 'studio',
        'created'   => time(),
    );
}

/**
 * Seed the built-in preset palettes on theme activation (idempotent).
 */
function poetheme_studio_seed_default_palettes() {
    if ( get_option( 'poetheme_presets_seeded' ) ) {
        return;
    }

    $palettes = poetheme_get_style_palettes();

    foreach ( poetheme_get_studio_presets() as $preset ) {
        $seeds = poetheme_studio_preset_to_seeds( $preset );

        $id = 'pal_' . wp_generate_password( 10, false, false );
        while ( isset( $palettes[ $id ] ) ) {
            $id = 'pal_' . wp_generate_password( 10, false, false );
        }

        $palettes[ $id ] = poetheme_studio_build_palette( $preset['name'], $seeds, 'preset' );
    }

    update_option( 'poetheme_style_palettes', $palettes );
    update_option( 'poetheme_presets_seeded', 1 );
}
add_action( 'after_switch_theme', 'poetheme_studio_seed_default_palettes' );


/**
 * Render a grouped (optgroup) font select for the Style Studio.
 *
 * @param string $data_attr     Data attribute hook (without brackets).
 * @param string $default_label Label for the "theme default" option.
 */
function poetheme_studio_render_font_select( $data_attr, $default_label ) {
    $available = function_exists( 'poetheme_get_available_fonts' ) ? poetheme_get_available_fonts() : array();
    ?>
    <select <?php echo esc_attr( $data_attr ); ?> <?php disabled( empty( $available ) ); ?>>
        <option value=""><?php echo esc_html( $default_label ); ?></option>
        <?php foreach ( poetheme_get_font_families( $available ) as $family_label => $family_fonts ) : ?>
            <optgroup label="<?php echo esc_attr( $family_label ); ?>">
                <?php foreach ( $family_fonts as $font ) : ?>
                    <option value="<?php echo esc_attr( $font['slug'] ); ?>" data-font-family="<?php echo esc_attr( $font['family'] ); ?>" data-preview-family="poetheme-pv-<?php echo esc_attr( $font['slug'] ); ?>">
                        <?php echo esc_html( $font['variant_label'] ); ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Advanced (per-token) override controls for Style Studio.
 *
 * Each field maps a single control to one or more real token keys; editing it
 * overrides all listed keys on top of the generated value. The first key is the
 * representative used to display the current value. The control type drives the
 * input and which override bucket it belongs to (colors / fonts / global).
 *
 * @return array{colors:array,sizes:array,layout:array}
 */
function poetheme_get_studio_advanced_fields() {
    return array(
        'colors' => array(
            array( 'label' => __( 'Sfondo pagina', 'poetheme' ), 'type' => 'color', 'keys' => array( 'page_background_color' ) ),
            array( 'label' => __( 'Sfondo contenuto', 'poetheme' ), 'type' => 'color', 'keys' => array( 'content_background_color' ) ),
            array( 'label' => __( 'Testo', 'poetheme' ), 'type' => 'color', 'keys' => array( 'content_text_color' ) ),
            array( 'label' => __( 'Titoli', 'poetheme' ), 'type' => 'color', 'keys' => array( 'heading_h1_color', 'heading_h2_color', 'heading_h3_color', 'heading_h4_color', 'heading_h5_color', 'heading_h6_color', 'content_strong_color', 'page_title_color', 'post_title_color', 'category_title_color' ) ),
            array( 'label' => __( 'Link', 'poetheme' ), 'type' => 'color', 'keys' => array( 'content_link_color', 'general_link_color', 'menu_active_link_color', 'footer_widget_link_color' ) ),
            array( 'label' => __( 'Voci di menu', 'poetheme' ), 'type' => 'color', 'keys' => array( 'menu_link_color' ) ),
            array( 'label' => __( 'Sfondo testata', 'poetheme' ), 'type' => 'color', 'keys' => array( 'header_background_color' ) ),
            array( 'label' => __( 'Sfondo pulsante CTA', 'poetheme' ), 'type' => 'color', 'keys' => array( 'cta_background_color' ) ),
            array( 'label' => __( 'Testo pulsante CTA', 'poetheme' ), 'type' => 'color', 'keys' => array( 'cta_text_color' ) ),
            array( 'label' => __( 'Sfondo top bar', 'poetheme' ), 'type' => 'color', 'keys' => array( 'top_bar_background_color' ) ),
            array( 'label' => __( 'Testo top bar', 'poetheme' ), 'type' => 'color', 'keys' => array( 'top_bar_text_color', 'top_bar_link_color', 'top_bar_icon_color' ) ),
            array( 'label' => __( 'Sfondo footer', 'poetheme' ), 'type' => 'color', 'keys' => array( 'footer_widget_background_color' ) ),
            array( 'label' => __( 'Testo footer', 'poetheme' ), 'type' => 'color', 'keys' => array( 'footer_widget_text_color' ) ),
            array( 'label' => __( 'Titoli footer', 'poetheme' ), 'type' => 'color', 'keys' => array( 'footer_widget_heading_h2_color', 'footer_widget_heading_h3_color', 'footer_widget_heading_h4_color', 'footer_widget_heading_h5_color' ) ),
        ),
        'sizes'  => array(
            array( 'label' => __( 'Dimensione testo', 'poetheme' ), 'type' => 'size', 'keys' => array( 'body_font_size' ) ),
            array( 'label' => __( 'Dimensione H1', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_font_size', 'page_title_font_size', 'post_title_font_size' ) ),
            array( 'label' => __( 'Dimensione H2', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_h2_font_size', 'category_title_font_size' ) ),
            array( 'label' => __( 'Dimensione H3', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_h3_font_size' ) ),
            array( 'label' => __( 'Dimensione H4', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_h4_font_size' ) ),
            array( 'label' => __( 'Dimensione H5', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_h5_font_size' ) ),
            array( 'label' => __( 'Dimensione H6', 'poetheme' ), 'type' => 'size', 'keys' => array( 'heading_h6_font_size' ) ),
        ),
        'layout' => array(
            array( 'label' => __( 'Larghezza sito', 'poetheme' ), 'type' => 'width', 'keys' => array( 'site_width' ) ),
            array( 'label' => __( 'Layout', 'poetheme' ), 'type' => 'layout', 'keys' => array( 'layout_mode' ) ),
            array( 'label' => __( 'Raggio pulsanti', 'poetheme' ), 'type' => 'radius', 'keys' => array( 'cta_button_border_radius' ) ),
            array( 'label' => __( 'Sottolinea i link', 'poetheme' ), 'type' => 'bool', 'keys' => array( 'content_link_underline' ) ),
        ),
    );
}

/**
 * Render a single advanced override control.
 *
 * @param array $field Field definition (label, type, keys).
 */
function poetheme_studio_render_advanced_field( $field ) {
    $type = $field['type'];
    $keys = implode( ',', $field['keys'] );
    ?>
    <div class="poetheme-studio__adv-field" data-adv-keys="<?php echo esc_attr( $keys ); ?>" data-adv-type="<?php echo esc_attr( $type ); ?>">
        <span class="poetheme-studio__adv-label"><?php echo esc_html( $field['label'] ); ?></span>
        <span class="poetheme-studio__adv-control">
            <?php
            switch ( $type ) {
                case 'color':
                    echo '<input type="color" data-adv-input value="#000000" />';
                    break;
                case 'size':
                    echo '<input type="number" data-adv-input min="0.5" max="6" step="0.05" value="1" /><span class="poetheme-studio__adv-unit">rem</span>';
                    break;
                case 'radius':
                    echo '<input type="number" data-adv-input min="0" max="999" step="1" value="8" /><span class="poetheme-studio__adv-unit">px</span>';
                    break;
                case 'width':
                    echo '<input type="number" data-adv-input min="960" max="1920" step="10" value="1280" /><span class="poetheme-studio__adv-unit">px</span>';
                    break;
                case 'bool':
                    echo '<input type="checkbox" data-adv-input />';
                    break;
                case 'layout':
                    echo '<select data-adv-input><option value="full">' . esc_html__( 'Largo', 'poetheme' ) . '</option><option value="boxed">' . esc_html__( 'Riquadro', 'poetheme' ) . '</option></select>';
                    break;
            }
            ?>
            <button type="button" class="poetheme-studio__adv-reset" data-adv-reset title="<?php esc_attr_e( 'Reimposta al valore generato', 'poetheme' ); ?>" hidden>↺</button>
        </span>
    </div>
    <?php
}

/**
 * Render the Style Studio page.
 */
function poetheme_render_style_studio_page() {
    if ( ! poetheme_user_can_manage_options() ) {
        wp_die( esc_html__( 'Permesso negato.', 'poetheme' ) );
    }

    $post_url  = esc_url( admin_url( 'admin-post.php' ) );
    $harmonies = array(
        'complementary' => __( 'Complementare', 'poetheme' ),
        'analogous'     => __( 'Analoga', 'poetheme' ),
        'triadic'       => __( 'Triade', 'poetheme' ),
        'split'         => __( 'Complementare divisa', 'poetheme' ),
        'monochromatic' => __( 'Monocromatica', 'poetheme' ),
    );
    ?>
    <div class="wrap poetheme-options poetheme-studio">
        <h1><?php esc_html_e( 'Style Studio', 'poetheme' ); ?></h1>
        <p class="description">
            <?php esc_html_e( 'Style Studio è lo strumento per creare il design del sito: scegli un colore brand, una regola di armonia e la tipografia, e il tema genera automaticamente una combinazione coerente per tutti gli elementi. Salva il risultato come palette, gestibile in “Palette e stile”.', 'poetheme' ); ?>
        </p>

        <p class="poetheme-studio__toolbar">
            <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=poetheme-palette' ) ); ?>">&larr; <?php esc_html_e( 'Palette e stile', 'poetheme' ); ?></a>
            <button type="button" class="button" data-studio-random>🎲 <?php esc_html_e( 'Ispirami', 'poetheme' ); ?></button>
            <span class="poetheme-studio__editing" data-studio-editing hidden></span>
        </p>

        <form action="<?php echo $post_url; ?>" method="post" class="poetheme-studio__form" data-poetheme-studio>
            <input type="hidden" name="action" value="poetheme_style_studio_save" />
            <?php wp_nonce_field( 'poetheme_style_studio_save' ); ?>
            <input type="hidden" name="poetheme_studio_payload" value="" data-studio-payload />

            <div class="poetheme-studio__layout">
                <div class="poetheme-studio__controls">
                    <p class="poetheme-field">
                        <label for="poetheme-studio-name"><strong><?php esc_html_e( 'Nome template', 'poetheme' ); ?></strong></label>
                        <input type="text" id="poetheme-studio-name" name="palette_name" class="regular-text" data-studio-name value="<?php esc_attr_e( 'La mia palette', 'poetheme' ); ?>" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-base"><strong><?php esc_html_e( 'Colore brand', 'poetheme' ); ?></strong></label><br />
                        <input type="color" id="poetheme-studio-base" data-studio-base value="#2563eb" />
                        <input type="text" class="poetheme-studio__hex" data-studio-base-hex value="#2563eb" maxlength="7" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-harmony"><strong><?php esc_html_e( 'Armonia', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-harmony" data-studio-harmony>
                            <?php foreach ( $harmonies as $key => $label ) : ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-mode"><strong><?php esc_html_e( 'Modalità', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-mode" data-studio-mode>
                            <option value="light"><?php esc_html_e( 'Chiara', 'poetheme' ); ?></option>
                            <option value="dark"><?php esc_html_e( 'Scura', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label>
                            <input type="checkbox" data-studio-accent-buttons />
                            <?php esc_html_e( 'Usa il colore accento per i pulsanti', 'poetheme' ); ?>
                        </label>
                    </p>

                    <div class="poetheme-studio__palette" data-studio-swatches aria-hidden="true"></div>
                    <div class="poetheme-studio__contrast" data-studio-contrast></div>

                    <hr />
                    <h2 class="poetheme-studio__section"><?php esc_html_e( 'Tipografia', 'poetheme' ); ?></h2>

                    <p class="poetheme-field">
                        <label><strong><?php esc_html_e( 'Font titoli', 'poetheme' ); ?></strong></label><br />
                        <?php poetheme_studio_render_font_select( 'data-studio-heading-font', __( 'Predefinito del tema', 'poetheme' ) ); ?>
                    </p>

                    <p class="poetheme-field">
                        <label><strong><?php esc_html_e( 'Font testo', 'poetheme' ); ?></strong></label><br />
                        <?php poetheme_studio_render_font_select( 'data-studio-body-font', __( 'Predefinito del tema', 'poetheme' ) ); ?>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-base-size"><strong><?php esc_html_e( 'Dimensione base testo (rem)', 'poetheme' ); ?></strong></label><br />
                        <input type="number" id="poetheme-studio-base-size" data-studio-base-size value="1" min="0.8" max="1.5" step="0.05" />
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-ratio"><strong><?php esc_html_e( 'Scala tipografica', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-ratio" data-studio-ratio>
                            <option value="1.125"><?php esc_html_e( 'Compatta (1.125)', 'poetheme' ); ?></option>
                            <option value="1.2"><?php esc_html_e( 'Terza minore (1.2)', 'poetheme' ); ?></option>
                            <option value="1.25" selected><?php esc_html_e( 'Terza maggiore (1.25)', 'poetheme' ); ?></option>
                            <option value="1.333"><?php esc_html_e( 'Quarta giusta (1.333)', 'poetheme' ); ?></option>
                            <option value="1.414"><?php esc_html_e( 'Ampia (1.414)', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <hr />
                    <h2 class="poetheme-studio__section"><?php esc_html_e( 'Densità e forme', 'poetheme' ); ?></h2>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-density"><strong><?php esc_html_e( 'Densità', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-density" data-studio-density>
                            <option value="compact"><?php esc_html_e( 'Compatta', 'poetheme' ); ?></option>
                            <option value="comfortable" selected><?php esc_html_e( 'Comoda', 'poetheme' ); ?></option>
                            <option value="spacious"><?php esc_html_e( 'Ariosa', 'poetheme' ); ?></option>
                        </select>
                    </p>

                    <p class="poetheme-field">
                        <label for="poetheme-studio-radius"><strong><?php esc_html_e( 'Arrotondamento pulsanti', 'poetheme' ); ?></strong></label><br />
                        <select id="poetheme-studio-radius" data-studio-radius>
                            <option value="0"><?php esc_html_e( 'Squadrato', 'poetheme' ); ?></option>
                            <option value="8" selected><?php esc_html_e( 'Morbido', 'poetheme' ); ?></option>
                            <option value="999"><?php esc_html_e( 'Pillola', 'poetheme' ); ?></option>
                        </select>
                    </p>
                </div>

                <div class="poetheme-studio__preview" data-studio-preview aria-label="<?php esc_attr_e( 'Anteprima', 'poetheme' ); ?>"></div>
            </div>

            <?php $advanced = poetheme_get_studio_advanced_fields(); ?>
            <details class="poetheme-studio__advanced" data-studio-advanced>
                <summary>
                    <strong><?php esc_html_e( 'Personalizzazione avanzata', 'poetheme' ); ?></strong>
                    <span class="poetheme-studio__adv-hint"><?php esc_html_e( 'Rifinisci i singoli colori e le dimensioni partendo dai valori generati.', 'poetheme' ); ?></span>
                </summary>

                <div class="poetheme-studio__adv-body">
                    <p class="poetheme-studio__adv-actions">
                        <button type="button" class="button button-link" data-studio-adv-reset-all><?php esc_html_e( 'Reimposta tutte le personalizzazioni', 'poetheme' ); ?></button>
                    </p>

                    <h3 class="poetheme-studio__section"><?php esc_html_e( 'Colori', 'poetheme' ); ?></h3>
                    <div class="poetheme-studio__adv-grid">
                        <?php foreach ( $advanced['colors'] as $field ) {
                            poetheme_studio_render_advanced_field( $field );
                        } ?>
                    </div>

                    <h3 class="poetheme-studio__section"><?php esc_html_e( 'Dimensioni testo', 'poetheme' ); ?></h3>
                    <div class="poetheme-studio__adv-grid">
                        <?php foreach ( $advanced['sizes'] as $field ) {
                            poetheme_studio_render_advanced_field( $field );
                        } ?>
                    </div>

                    <h3 class="poetheme-studio__section"><?php esc_html_e( 'Layout e dettagli', 'poetheme' ); ?></h3>
                    <div class="poetheme-studio__adv-grid">
                        <?php foreach ( $advanced['layout'] as $field ) {
                            poetheme_studio_render_advanced_field( $field );
                        } ?>
                    </div>
                </div>
            </details>

            <p class="poetheme-studio__actions">
                <button type="submit" class="button button-secondary" name="apply" value="0" data-studio-save
                    data-label-create="<?php esc_attr_e( 'Salva come palette', 'poetheme' ); ?>"
                    data-label-update="<?php esc_attr_e( 'Aggiorna palette', 'poetheme' ); ?>"><?php esc_html_e( 'Salva come palette', 'poetheme' ); ?></button>
                <button type="submit" class="button button-primary" name="apply" value="1" data-studio-save-apply
                    data-label-create="<?php esc_attr_e( 'Salva e applica', 'poetheme' ); ?>"
                    data-label-update="<?php esc_attr_e( 'Aggiorna e applica', 'poetheme' ); ?>"><?php esc_html_e( 'Salva e applica', 'poetheme' ); ?></button>
            </p>
        </form>
    </div>
    <?php
}
