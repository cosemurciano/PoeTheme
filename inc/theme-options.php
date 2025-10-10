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
 * Register theme settings.
 */
function poetheme_get_default_global_options() {
    return array(
        'layout_mode' => 'full',
        'site_width'  => 1200,
    );
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

    return array(
        'layout_mode' => $layout_mode,
        'site_width'  => $width,
    );
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

    return $options;
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
        'poetheme_logo_group',
        'poetheme_logo',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'poetheme_sanitize_logo_options',
            'default'           => array(
                'logo_id' => 0,
            ),
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
    $options      = poetheme_get_global_options();
    $layout_mode  = isset( $options['layout_mode'] ) ? $options['layout_mode'] : 'full';
    $site_width   = isset( $options['site_width'] ) ? absint( $options['site_width'] ) : 1200;
    $width_id     = 'poetheme-global-site-width';
    $layout_field = 'poetheme_global[layout_mode]';
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

/**
 * Render the logo settings page.
 */
function poetheme_render_logo_page() {
    $options = poetheme_get_logo_options();
    $logo_id         = $options['logo_id'];
    $logo            = $logo_id ? wp_get_attachment_image_src( $logo_id, 'medium' ) : false;
    $logo_height     = isset( $options['logo_height'] ) ? absint( $options['logo_height'] ) : 0;
    $show_site_title = ! empty( $options['show_site_title'] );
    $title_color     = isset( $options['title_color'] ) ? $options['title_color'] : '#111827';
    $title_size      = isset( $options['title_size'] ) ? absint( $options['title_size'] ) : 0;
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
        $title_style_attr .= 'font-size:' . $title_size . 'px;';
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
                    <input type="color" id="poetheme_logo_title_color" name="poetheme_logo[title_color]" value="<?php echo esc_attr( $title_color ); ?>" />
                </p>
                <p>
                    <label for="poetheme_logo_title_size"><?php esc_html_e( 'Dimensione del titolo (px)', 'poetheme' ); ?></label><br />
                    <input type="number" min="16" step="1" id="poetheme_logo_title_size" name="poetheme_logo[title_size]" value="<?php echo esc_attr( $title_size ); ?>" class="small-text" />
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
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Sottointestazione', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Le impostazioni della sottointestazione saranno disponibili a breve.', 'poetheme' ); ?></p>
    </div>
    <?php
}

/**
 * Render the footer settings page.
 */
function poetheme_render_footer_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Piè di pagina', 'poetheme' ); ?></h1>
        <p class="description"><?php esc_html_e( 'Le impostazioni del piè di pagina saranno disponibili a breve.', 'poetheme' ); ?></p>
    </div>
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
function poetheme_get_logo_options() {
    $defaults = array(
        'logo_id'        => 0,
        'logo_height'    => 48,
        'show_site_title'=> false,
        'title_color'    => '#111827',
        'title_size'     => 32,
    );

    $options = get_option( 'poetheme_logo', array() );

    $options = wp_parse_args( $options, $defaults );

    $options['logo_id']        = absint( $options['logo_id'] );
    $options['logo_height']    = isset( $options['logo_height'] ) ? absint( $options['logo_height'] ) : $defaults['logo_height'];
    $options['show_site_title']= ! empty( $options['show_site_title'] );

    $color = isset( $options['title_color'] ) ? sanitize_hex_color( $options['title_color'] ) : '';
    $options['title_color'] = $color ? $color : $defaults['title_color'];

    $size = isset( $options['title_size'] ) ? absint( $options['title_size'] ) : 0;
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
        $size = absint( $input['title_size'] );
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
    $screens = array(
        'toplevel_page_poetheme-settings',
        'poetheme_page_poetheme-logo',
        'poetheme_page_poetheme-header',
        'poetheme_page_poetheme-subheader',
        'poetheme_page_poetheme-footer',
        'poetheme_page_poetheme-custom-css',
    );

    if ( in_array( $hook, $screens, true ) ) {
        wp_enqueue_style( 'poetheme-theme-options', POETHEME_URI . '/assets/css/theme-options.css', array(), POETHEME_VERSION );
    }

    if ( 'poetheme_page_poetheme-logo' === $hook ) {
        wp_enqueue_media();
        wp_enqueue_script( 'poetheme-theme-options', POETHEME_URI . '/assets/js/theme-options.js', array( 'jquery' ), POETHEME_VERSION, true );
        wp_localize_script(
            'poetheme-theme-options',
            'poethemeThemeOptions',
            array(
                'chooseLogo' => __( 'Scegli un logo', 'poetheme' ),
                'selectLogo' => __( 'Usa questo logo', 'poetheme' ),
                'noLogo'     => __( 'Nessun logo selezionato.', 'poetheme' ),
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
