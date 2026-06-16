<?php
/**
 * Color option defaults, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

function poetheme_sanitize_color_options( $input ) {
    $defaults = poetheme_get_default_color_options();
    $output   = array();
    $boolean_keys = array(
        'content_link_underline',
        'header_background_transparent',
        'header_disable_shadow',
        'footer_widget_background_transparent',
    );

    if ( ! poetheme_user_can_manage_options() ) {
        return poetheme_get_color_options();
    }

    if ( ! is_array( $input ) ) {
        $input = array();
    }

    foreach ( $defaults as $key => $default_value ) {
        if ( in_array( $key, $boolean_keys, true ) ) {
            $output[ $key ] = ! empty( $input[ $key ] );
            continue;
        }

        if ( is_array( $default_value ) ) {
            $raw_value = isset( $input[ $key ] ) ? $input[ $key ] : array();
            $output[ $key ] = poetheme_sanitize_spacing_group( $raw_value, $default_value );
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

        if ( is_array( $default_value ) ) {
            $raw_value       = isset( $raw[ $key ] ) ? $raw[ $key ] : array();
            $options[ $key ] = poetheme_sanitize_spacing_group( $raw_value, $default_value );
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
        $description_id = ! empty( $field['description'] ) ? $field_id . '-description' : '';
        ?>
        <div class="poetheme-color-section__field">
            <label class="poetheme-color-section__label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>

            <div class="poetheme-color-section__control">
                <?php if ( 'toggle' === $type ) : ?>
                    <select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>"<?php echo $description_id ? ' aria-describedby="' . esc_attr( $description_id ) . '"' : ''; ?>>
                        <option value="0" <?php selected( false, ! empty( $value ) ); ?>><?php esc_html_e( 'No', 'poetheme' ); ?></option>
                        <option value="1" <?php selected( true, ! empty( $value ) ); ?>><?php esc_html_e( 'Sì', 'poetheme' ); ?></option>
                    </select>
                <?php elseif ( 'spacing' === $type ) : ?>
                    <?php
                    $segments   = array(
                        'margin'  => __( 'Margine', 'poetheme' ),
                        'padding' => __( 'Padding', 'poetheme' ),
                    );
                    $directions = array(
                        'top'    => __( 'Alto', 'poetheme' ),
                        'right'  => __( 'Destra', 'poetheme' ),
                        'bottom' => __( 'Basso', 'poetheme' ),
                        'left'   => __( 'Sinistra', 'poetheme' ),
                    );
                    $value = is_array( $value ) ? $value : array();
                    ?>
                    <div class="poetheme-spacing-control" role="group" aria-labelledby="<?php echo esc_attr( $field_id ); ?>"<?php echo $description_id ? ' aria-describedby="' . esc_attr( $description_id ) . '"' : ''; ?>>
                        <?php foreach ( $segments as $segment_key => $segment_label ) :
                            $segment_values = isset( $value[ $segment_key ] ) && is_array( $value[ $segment_key ] ) ? $value[ $segment_key ] : array();
                            ?>
                            <div class="poetheme-spacing-row">
                                <span class="poetheme-spacing-row__label"><?php echo esc_html( $segment_label ); ?></span>
                                <div class="poetheme-spacing-row__inputs">
                                    <?php foreach ( $directions as $direction_key => $direction_label ) :
                                        $input_id    = $field_id . '-' . $segment_key . '-' . $direction_key;
                                        $input_name  = $field_name . '[' . $segment_key . '][' . $direction_key . ']';
                                        $input_value = isset( $segment_values[ $direction_key ] ) ? $segment_values[ $direction_key ] : '';
                                        ?>
                                        <label class="poetheme-spacing-input" for="<?php echo esc_attr( $input_id ); ?>">
                                            <span class="poetheme-spacing-input__label"><?php echo esc_html( $direction_label ); ?></span>
                                            <input
                                                type="text"
                                                id="<?php echo esc_attr( $input_id ); ?>"
                                                name="<?php echo esc_attr( $input_name ); ?>"
                                                value="<?php echo esc_attr( $input_value ); ?>"
                                                placeholder="0"
                                            />
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                            <?php echo $description_id ? ' aria-describedby="' . esc_attr( $description_id ) . '"' : ''; ?>
                        />
                        <span class="poetheme-color-preview" data-preview-for="<?php echo esc_attr( $field_id ); ?>" style="--poetheme-preview-color: <?php echo esc_attr( $preview ); ?>;"></span>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $field['description'] ) ) : ?>
                    <p id="<?php echo esc_attr( $description_id ); ?>" class="description poetheme-color-section__help"><?php echo esc_html( $field['description'] ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    };
    ?>
    <div class="wrap poetheme-admin poetheme-color-settings">
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

                                        if ( 'spacing' === $type ) {
                                            $preview_color = 'transparent';
                                        } else {
                                            if ( '' === $preview_color && '' !== $default ) {
                                                $preview_color = $default;
                                            }

                                            if ( '' === $preview_color ) {
                                                $preview_color = 'transparent';
                                            }
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
