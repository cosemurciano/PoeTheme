<?php
/**
 * Header and logo option defaults, sanitization, and admin pages.
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
 * Retrieve the available header layout choices.
 *
 * @return array
 */

function poetheme_get_header_layout_choices() {
    return array(
        'style-1' => array(
            'label'       => __( 'Classic', 'poetheme' ),
            'description' => __( 'Layout classico con logo, navigazione e opzioni top bar.', 'poetheme' ),
            'image'       => 'classic.png',
        ),
        'style-2' => array(
            'label'       => __( 'Split menu | Semitransparent', 'poetheme' ),
            'description' => __( 'Layout con menu diviso e testata semitrasparente.', 'poetheme' ),
            'image'       => 'split-menu-semitransparent.png',
        ),
        'style-3' => array(
            'label'       => __( 'Shop split', 'poetheme' ),
            'description' => __( 'Layout shop con navigazione divisa.', 'poetheme' ),
            'image'       => 'shop-split.png',
        ),
        'style-4' => array(
            'label'       => __( 'Shop', 'poetheme' ),
            'description' => __( 'Layout shop compatto con azioni in testata.', 'poetheme' ),
            'image'       => 'shop.png',
        ),
        'style-5' => array(
            'label'       => __( 'Fixed', 'poetheme' ),
            'description' => __( 'Layout con testata fissa.', 'poetheme' ),
            'image'       => 'fixed.png',
        ),
        'style-6' => array(
            'label'       => __( 'Stack | Center', 'poetheme' ),
            'description' => __( 'Layout impilato con logo centrato.', 'poetheme' ),
            'image'       => 'stack-center.png',
        ),
        'style-7' => array(
            'label'       => __( 'Stack | Left', 'poetheme' ),
            'description' => __( 'Layout impilato con logo allineato a sinistra.', 'poetheme' ),
            'image'       => 'stack-left.png',
        ),
        'style-8' => array(
            'label'       => __( 'Plain', 'poetheme' ),
            'description' => __( 'Layout essenziale e minimale.', 'poetheme' ),
            'image'       => 'plain.png',
        ),
        'style-9' => array(
            'label'       => __( 'App Sidebar', 'poetheme' ),
            'description' => __( 'Layout con sidebar verticale collassabile, logo in alto, menu laterale, titolo pagina e breadcrumb nell’area contenuto.', 'poetheme' ),
            'image'       => '',
            'preview'     => 'app-sidebar',
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
        'cta_text'                     => __( 'Get Started', 'poetheme' ),
        'cta_url'                      => home_url( '/' ),
        'social_links'                 => $social_defaults,
        'show_app_header_intro'        => false,
        'app_header_intro_title'       => '',
        'app_header_intro_description' => '',
    );
}

/**
 * Default values for blog options.
 *
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
        if ( isset( poetheme_get_header_layout_choices()[ $layout ] ) ) {
            $output['layout'] = $layout;
        }
    }

    $output['show_top_bar']          = ! empty( $input['show_top_bar'] );
    $output['show_cta']              = ! empty( $input['show_cta'] );
    $output['show_app_header_intro'] = ! empty( $input['show_app_header_intro'] );

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

    $output['app_header_intro_title']       = isset( $input['app_header_intro_title'] ) ? sanitize_text_field( $input['app_header_intro_title'] ) : $defaults['app_header_intro_title'];
    $output['app_header_intro_description'] = isset( $input['app_header_intro_description'] ) ? sanitize_textarea_field( $input['app_header_intro_description'] ) : $defaults['app_header_intro_description'];

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
    if ( ! isset( poetheme_get_header_layout_choices()[ $layout ] ) ) {
        $layout = $defaults['layout'];
    }
    $options['layout'] = $layout;

    $options['show_top_bar']          = ! empty( $options['show_top_bar'] );
    $options['show_cta']              = ! empty( $options['show_cta'] );
    $options['show_app_header_intro'] = ! empty( $options['show_app_header_intro'] );

    $options['app_header_intro_title']       = isset( $options['app_header_intro_title'] ) ? (string) $options['app_header_intro_title'] : $defaults['app_header_intro_title'];
    $options['app_header_intro_description'] = isset( $options['app_header_intro_description'] ) ? (string) $options['app_header_intro_description'] : $defaults['app_header_intro_description'];

    return $options;
}

/**
 * Retrieve blog options with defaults.
 *
 * @return array
 */

function poetheme_render_header_page() {
    $options       = poetheme_get_header_options();
    $layouts       = poetheme_get_header_layout_choices();
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
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Intestazione', 'poetheme' ); ?></h1>
        <form action="options.php" method="post" class="poetheme-options-form">
            <?php settings_fields( 'poetheme_header_group' ); ?>
            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Impostazioni testata', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Configura layout, top bar, call to action e profili social.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <table class="form-table poetheme-fields" role="presentation">
                        <tbody>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Seleziona layout', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <div class="poetheme-header-layouts" role="radiogroup" aria-label="<?php esc_attr_e( 'Seleziona layout', 'poetheme' ); ?>" aria-describedby="poetheme-header-layout-help">
                                        <?php foreach ( $layouts as $layout_key => $layout ) : ?>
                                            <?php $layout_id = 'poetheme_header_layout_' . $layout_key; ?>
                                            <label class="poetheme-header-layout" for="<?php echo esc_attr( $layout_id ); ?>">
                                                <input
                                                    id="<?php echo esc_attr( $layout_id ); ?>"
                                                    type="radio"
                                                    class="poetheme-header-layout__input"
                                                    name="poetheme_header[layout]"
                                                    value="<?php echo esc_attr( $layout_key ); ?>"
                                                    <?php checked( $options['layout'], $layout_key ); ?>
                                                    aria-label="<?php echo esc_attr( $layout['label'] ); ?>"
                                                />
                                                <span class="poetheme-header-layout__card">
                                                    <?php if ( ! empty( $layout['image'] ) ) : ?>
                                                        <img
                                                            src="<?php echo esc_url( POETHEME_URI . '/assets/img/admin/header-layouts/' . $layout['image'] ); ?>"
                                                            alt="<?php echo esc_attr( $layout['label'] ); ?>"
                                                            class="poetheme-header-layout__image"
                                                        />
                                                    <?php else : ?>
                                                        <span class="poetheme-header-layout__preview poetheme-header-layout__preview--<?php echo esc_attr( isset( $layout['preview'] ) ? sanitize_html_class( $layout['preview'] ) : sanitize_html_class( $layout_key ) ); ?>" aria-hidden="true">
                                                            <span class="poetheme-header-layout__preview-sidebar">
                                                                <span></span><span></span><span></span><span></span>
                                                            </span>
                                                            <span class="poetheme-header-layout__preview-main">
                                                                <span></span><span></span><span></span>
                                                            </span>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="poetheme-header-layout__name"><?php echo esc_html( $layout['label'] ); ?></span>
                                                    <?php if ( ! empty( $layout['description'] ) ) : ?>
                                                        <span class="poetheme-header-layout__description"><?php echo esc_html( $layout['description'] ); ?></span>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <p id="poetheme-header-layout-help" class="description poetheme-field__help"><?php esc_html_e( "Scegli quale testata applicare al tema.", 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Barra superiore', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <label for="poetheme_header_show_top_bar">
                                        <input type="checkbox" id="poetheme_header_show_top_bar" name="poetheme_header[show_top_bar]" value="1" <?php checked( ! empty( $options['show_top_bar'] ) ); ?> aria-describedby="poetheme-header-topbar-help" />
                                        <?php esc_html_e( 'Mostra la barra superiore con informazioni e social.', 'poetheme' ); ?>
                                    </label>
                                    <p id="poetheme-header-topbar-help" class="description poetheme-field__help"><?php esc_html_e( 'La barra comprende messaggio iniziale, contatti, posizione, un menù informativo e le icone social.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label">
                            <label for="poetheme_header_top_text_info"><?php esc_html_e( 'Messaggio iniziale', 'poetheme' ); ?></label>
                        </th>
                        <td class="poetheme-field__control">
                            <input type="text" id="poetheme_header_top_text_info" name="poetheme_header[top_bar_texts][text_1]" value="<?php echo esc_attr( $top_bar_texts['text_1'] ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label">
                            <label for="poetheme_header_top_text_email"><?php esc_html_e( 'Email', 'poetheme' ); ?></label>
                        </th>
                        <td class="poetheme-field__control">
                            <input type="email" id="poetheme_header_top_text_email" name="poetheme_header[top_bar_texts][email]" value="<?php echo esc_attr( $top_bar_texts['email'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'esempio@dominio.it', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label">
                            <label for="poetheme_header_top_text_phone"><?php esc_html_e( 'Telefono', 'poetheme' ); ?></label>
                        </th>
                        <td class="poetheme-field__control">
                            <input type="text" id="poetheme_header_top_text_phone" name="poetheme_header[top_bar_texts][phone]" value="<?php echo esc_attr( $top_bar_texts['phone'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '+39 012 3456789', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label">
                            <label for="poetheme_header_top_text_whatsapp"><?php esc_html_e( 'WhatsApp', 'poetheme' ); ?></label>
                        </th>
                        <td class="poetheme-field__control">
                            <input type="text" id="poetheme_header_top_text_whatsapp" name="poetheme_header[top_bar_texts][whatsapp]" value="<?php echo esc_attr( $top_bar_texts['whatsapp'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '+39 012 3456789', 'poetheme' ); ?>" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Call to Action', 'poetheme' ); ?></th>
                        <td class="poetheme-field__control">
                            <label for="poetheme_header_show_cta">
                                <input type="checkbox" id="poetheme_header_show_cta" name="poetheme_header[show_cta]" value="1" <?php checked( $show_cta ); ?> aria-describedby="poetheme-header-cta-help" />
                                <?php esc_html_e( 'Mostra il pulsante Call to Action.', 'poetheme' ); ?>
                            </label>
                            <p id="poetheme-header-cta-help" class="description poetheme-field__help"><?php esc_html_e( 'Deseleziona per nascondere il pulsante in tutte le testate.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_cta_text" class="screen-reader-text"><?php esc_html_e( 'Testo pulsante', 'poetheme' ); ?></label>
                            <input type="text" id="poetheme_header_cta_text" name="poetheme_header[cta_text]" value="<?php echo esc_attr( $options['cta_text'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Get Started', 'poetheme' ); ?>" />
                            <label for="poetheme_header_cta_url" class="screen-reader-text"><?php esc_html_e( 'Link pulsante', 'poetheme' ); ?></label>
                            <input type="url" id="poetheme_header_cta_url" name="poetheme_header[cta_url]" value="<?php echo esc_attr( $options['cta_url'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label">
                            <label for="poetheme_header_top_text_location_label"><?php esc_html_e( 'Posizione', 'poetheme' ); ?></label>
                        </th>
                        <td class="poetheme-field__control">
                            <input type="text" id="poetheme_header_top_text_location_label" name="poetheme_header[top_bar_texts][location_label]" value="<?php echo esc_attr( $top_bar_texts['location_label'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Es. Piazza del Duomo, Milano', 'poetheme' ); ?>" aria-describedby="poetheme-header-location-help" />
                            <p id="poetheme-header-location-help" class="description poetheme-field__help"><?php esc_html_e( 'Testo mostrato accanto all’icona della posizione.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_top_text_location_url" class="screen-reader-text"><?php esc_html_e( 'Link Google Maps', 'poetheme' ); ?></label>
                            <input type="url" id="poetheme_header_top_text_location_url" name="poetheme_header[top_bar_texts][location_url]" value="<?php echo esc_attr( $top_bar_texts['location_url'] ); ?>" class="regular-text" placeholder="https://maps.google.com/" />
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Fascia descrittiva App Sidebar', 'poetheme' ); ?></th>
                        <td class="poetheme-field__control">
                            <label for="poetheme_header_show_app_header_intro">
                                <input type="checkbox" id="poetheme_header_show_app_header_intro" name="poetheme_header[show_app_header_intro]" value="1" <?php checked( ! empty( $options['show_app_header_intro'] ) ); ?> aria-describedby="poetheme-header-app-intro-help" />
                                <?php esc_html_e( 'Mostra fascia descrittiva nel layout App Sidebar.', 'poetheme' ); ?>
                            </label>
                            <p id="poetheme-header-app-intro-help" class="description poetheme-field__help"><?php esc_html_e( 'Queste impostazioni si applicano al layout App Sidebar e visualizzano una fascia sopra il contenuto destro. Se titolo, descrizione e menu fascia sono vuoti, la fascia non viene mostrata.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_app_intro_title" class="poetheme-field__label"><?php esc_html_e( 'Titolo fascia', 'poetheme' ); ?></label>
                            <input type="text" id="poetheme_header_app_intro_title" name="poetheme_header[app_header_intro_title]" value="<?php echo esc_attr( $options['app_header_intro_title'] ); ?>" class="regular-text" aria-describedby="poetheme-header-app-intro-title-help" />
                            <p id="poetheme-header-app-intro-title-help" class="description poetheme-field__help"><?php esc_html_e( 'Se il campo resta vuoto, non verrà mostrato alcun titolo nella fascia.', 'poetheme' ); ?></p>
                            <label for="poetheme_header_app_intro_description" class="poetheme-field__label"><?php esc_html_e( 'Descrizione fascia', 'poetheme' ); ?></label>
                            <textarea id="poetheme_header_app_intro_description" name="poetheme_header[app_header_intro_description]" class="large-text" rows="3" aria-describedby="poetheme-header-app-intro-description-help"><?php echo esc_textarea( $options['app_header_intro_description'] ); ?></textarea>
                            <p id="poetheme-header-app-intro-description-help" class="description poetheme-field__help"><?php esc_html_e( 'Se il campo resta vuoto, non verrà mostrata alcuna descrizione nella fascia.', 'poetheme' ); ?></p>
                        </td>
                    </tr>
                    <tr class="poetheme-field">
                        <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Icone social', 'poetheme' ); ?></th>
                        <td class="poetheme-field__control">
                            <p id="poetheme-header-social-help" class="description poetheme-field__help"><?php esc_html_e( 'Inserisci gli URL dei tuoi profili social per mostrarne le icone nella barra superiore.', 'poetheme' ); ?></p>
                            <?php foreach ( $socials as $key => $social ) :
                                $value = isset( $options['social_links'][ $key ] ) ? $options['social_links'][ $key ] : '';
                                ?>
                                <div class="poetheme-field">
                                    <label class="poetheme-field__label" for="poetheme_header_social_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $social['label'] ); ?></label>
                                    <div class="poetheme-field__control">
                                        <input type="url" id="poetheme_header_social_<?php echo esc_attr( $key ); ?>" name="poetheme_header[social_links][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="https://" aria-describedby="poetheme-header-social-help" />
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Logo', 'poetheme' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_logo_group' ); ?>
            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Branding', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Carica il logo o usa il titolo del sito con colori personalizzati.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
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
                    <div class="poetheme-form-stack">
                        <div class="poetheme-field">
                            <div class="poetheme-field__control">
                                <div class="poetheme-background-actions">
                                    <button type="button" class="button button-secondary" id="poetheme-logo-upload"><?php esc_html_e( 'Carica logo', 'poetheme' ); ?></button>
                                    <button type="button" class="button" id="poetheme-logo-remove" <?php disabled( 0 === $logo_id ); ?>><?php esc_html_e( 'Rimuovi logo', 'poetheme' ); ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="poetheme-field">
                            <div class="poetheme-field__control">
                                <label for="poetheme_logo_show_site_title">
                                    <input type="checkbox" id="poetheme_logo_show_site_title" name="poetheme_logo[show_site_title]" value="1" <?php checked( $show_site_title ); ?> />
                                    <?php esc_html_e( 'Mostra il titolo del sito al posto del logo', 'poetheme' ); ?>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="poetheme-logo-options<?php echo $show_site_title ? '' : ' poetheme-form-stack'; ?>"<?php echo $show_site_title ? ' style="display:none;"' : ''; ?>>
                        <div class="poetheme-field">
                            <label class="poetheme-field__label" for="poetheme_logo_height"><?php esc_html_e( 'Altezza del logo (px)', 'poetheme' ); ?></label>
                            <div class="poetheme-field__control">
                                <input type="number" min="0" step="1" id="poetheme_logo_height" name="poetheme_logo[logo_height]" value="<?php echo esc_attr( $logo_height ); ?>" class="small-text" aria-describedby="poetheme-logo-height-help" />
                                <span id="poetheme-logo-height-help" class="description poetheme-field__help"><?php esc_html_e( 'Imposta 0 per utilizzare le proporzioni originali.', 'poetheme' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="poetheme-title-options<?php echo $show_site_title ? ' poetheme-form-stack' : ''; ?>"<?php echo $show_site_title ? '' : ' style="display:none;"'; ?>>
                        <div class="poetheme-field">
                            <label class="poetheme-field__label" for="poetheme_logo_title_color"><?php esc_html_e( 'Colore del titolo', 'poetheme' ); ?></label>
                            <div class="poetheme-field__control">
                                <input
                                    type="text"
                                    class="poetheme-color-field"
                                    id="poetheme_logo_title_color"
                                    name="poetheme_logo[title_color]"
                                    value="<?php echo esc_attr( $title_color ); ?>"
                                    data-default-color="<?php echo esc_attr( $logo_defaults['title_color'] ); ?>"
                                />
                            </div>
                        </div>
                        <div class="poetheme-field">
                            <label class="poetheme-field__label" for="poetheme_logo_title_size"><?php esc_html_e( 'Dimensione del titolo (rem)', 'poetheme' ); ?></label>
                            <div class="poetheme-field__control">
                                <input type="number" min="0.5" step="0.05" id="poetheme_logo_title_size" name="poetheme_logo[title_size]" value="<?php echo esc_attr( poetheme_format_number_for_css( $title_size ) ); ?>" class="small-text" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the header settings page.
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
 * Sanitize header options.
 *
 * @param array $input Raw input values.
 * @return array
 */
