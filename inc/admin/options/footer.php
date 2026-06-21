<?php
/**
 * Footer option defaults, sanitization, and admin page.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
    <div class="wrap poetheme-admin">
        <h1><?php esc_html_e( 'Piè di pagina', 'poetheme' ); ?></h1>

        <form action="options.php" method="post">
            <?php settings_fields( 'poetheme_footer_group' ); ?>

            <div class="poetheme-panel">
                <div class="poetheme-panel__header">
                    <h2><?php esc_html_e( 'Struttura e credits', 'poetheme' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Gestisci la visibilità del footer, le righe di widget e il testo dei credits.', 'poetheme' ); ?></p>
                </div>
                <div class="poetheme-panel__body">
                    <table class="form-table poetheme-fields" role="presentation">
                        <tbody>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Mostra il piè di pagina', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <label for="poetheme-footer-display">
                                        <input type="checkbox" id="poetheme-footer-display" name="poetheme_footer[display_footer]" value="1" <?php checked( $display_footer ); ?> aria-describedby="poetheme-footer-display-help" />
                                        <?php esc_html_e( 'Visualizza l’intera area footer.', 'poetheme' ); ?>
                                    </label>
                                    <p id="poetheme-footer-display-help" class="description poetheme-field__help"><?php esc_html_e( 'Disattiva per nascondere completamente i widget del piè di pagina e la sezione finale.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field">
                                <th scope="row" class="poetheme-field__label"><?php esc_html_e( 'Credits', 'poetheme' ); ?></th>
                                <td class="poetheme-field__control">
                                    <label for="poetheme-footer-display-credits">
                                        <input type="checkbox" id="poetheme-footer-display-credits" name="poetheme_footer[display_footer_credits]" value="1" <?php checked( $display_footer_credits ); ?> aria-describedby="poetheme-footer-credits-help" />
                                        <?php esc_html_e( 'Visualizza i credits.', 'poetheme' ); ?>
                                    </label>
                                    <p id="poetheme-footer-credits-help" class="description poetheme-field__help"><?php esc_html_e( 'Disattiva per nascondere la sezione con i credits nel piè di pagina.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                            <tr class="poetheme-field poetheme-footer-dependent">
                                <th scope="row" class="poetheme-field__label"><label for="poetheme-footer-rows"><?php esc_html_e( 'Numero di righe', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <select id="poetheme-footer-rows" name="poetheme_footer[rows]" aria-describedby="poetheme-footer-rows-help">
                                        <option value="1" <?php selected( $rows, 1 ); ?>><?php esc_html_e( '1 riga', 'poetheme' ); ?></option>
                                        <option value="2" <?php selected( $rows, 2 ); ?>><?php esc_html_e( '2 righe', 'poetheme' ); ?></option>
                                    </select>
                                    <p id="poetheme-footer-rows-help" class="description poetheme-field__help"><?php esc_html_e( 'Scegli se visualizzare una o due righe di widget nel piè di pagina. Il layout della seconda riga resta memorizzato: se torni da 1 a 2 righe, viene ripristinata l’ultima configurazione.', 'poetheme' ); ?></p>
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
                        <tr class="poetheme-field poetheme-footer-layout-row poetheme-footer-dependent" data-footer-row="<?php echo esc_attr( $row ); ?>">
                            <th scope="row" class="poetheme-field__label"><label for="<?php echo esc_attr( $field_id ); ?>"><?php printf( esc_html__( 'Layout riga %d', 'poetheme' ), $row ); ?></label></th>
                            <td class="poetheme-field__control">
                                <select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" aria-describedby="<?php echo esc_attr( $field_id ); ?>-help">
                                    <?php foreach ( $choices as $choice_key => $choice ) : ?>
                                        <option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( $selected, $choice_key ); ?>><?php echo esc_html( $choice['label'] ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p id="<?php echo esc_attr( $field_id ); ?>-help" class="description poetheme-field__help"><?php printf( esc_html__( 'Scegli quante colonne mostrare nella riga %d.', 'poetheme' ), $row ); ?></p>
                            </td>
                        </tr>
                    <?php endfor; ?>
                            <tr class="poetheme-field poetheme-footer-dependent poetheme-footer-credits-dependent">
                                <th scope="row" class="poetheme-field__label"><label for="poetheme-footer-credits-content"><?php esc_html_e( 'Testo credits', 'poetheme' ); ?></label></th>
                                <td class="poetheme-field__control">
                                    <div class="poetheme-editor-wrap" aria-describedby="poetheme-footer-credits-help">
                                        <?php
                                        wp_editor(
                                            $credits_content,
                                            'poetheme-footer-credits-content',
                                            array(
                                                'textarea_name' => 'poetheme_footer[credits_content]',
                                                'textarea_rows' => 6,
                                                'media_buttons' => true,
                                                'editor_class'  => 'poetheme-editor-textarea',
                                            )
                                        );
                                        ?>
                                    </div>
                                    <p id="poetheme-footer-credits-help" class="description poetheme-field__help"><?php esc_html_e( 'Inserisci testo o HTML per personalizzare i credits del sito.', 'poetheme' ); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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
