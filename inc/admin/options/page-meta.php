<?php
/**
 * Page settings meta box registration, rendering, and saving.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
