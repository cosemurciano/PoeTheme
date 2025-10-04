<?php
/**
 * Mega menu helpers and admin fields.
 *
 * @package PoeTheme
 */

/**
 * Retrieve the list of Lucide icons used in the picker.
 *
 * @return string[]
 */
function poetheme_get_lucide_icons() {
    return array(
        'activity',
        'airplay',
        'alarm-check',
        'alarm-clock',
        'alarm-minus',
        'alarm-plus',
        'alert-circle',
        'archive',
        'arrow-right',
        'award',
        'badge-check',
        'bell',
        'book',
        'bookmark',
        'briefcase',
        'building',
        'calendar',
        'camera',
        'chart-bar',
        'check-circle',
        'clipboard-list',
        'clock',
        'cloud',
        'code',
        'compass',
        'database',
        'file-text',
        'globe',
        'headphones',
        'heart',
        'help-circle',
        'home',
        'inbox',
        'layers',
        'layout',
        'life-buoy',
        'link',
        'list',
        'lock',
        'mail',
        'map',
        'megaphone',
        'menu',
        'message-circle',
        'monitor',
        'palette',
        'phone',
        'pie-chart',
        'play-circle',
        'settings',
        'shield',
        'shopping-bag',
        'shopping-cart',
        'smartphone',
        'sparkles',
        'star',
        'tag',
        'trophy',
        'truck',
        'user',
        'users',
        'watch',
        'zap',
        'zoom-in',
    );
}

/**
 * Add custom mega menu fields to nav menu items.
 *
 * @param int      $item_id Menu item ID.
 * @param WP_Post  $item    Menu item object.
 * @param int      $depth   Depth of menu item.
 */
function poetheme_nav_menu_item_custom_fields( $item_id, $item, $depth, $args = array() ) {
    $is_title   = (bool) get_post_meta( $item_id, '_poetheme_menu_is_title', true );
    $icon_value = trim( (string) get_post_meta( $item_id, '_poetheme_menu_icon', true ) );
    $icons      = poetheme_get_lucide_icons();
    ?>
    <div class="poetheme-menu-field poetheme-menu-field--title">
        <label for="edit-menu-item-poetheme-title-<?php echo esc_attr( $item_id ); ?>" style="display:flex;align-items:center;gap:0.5rem;">
            <input type="checkbox" id="edit-menu-item-poetheme-title-<?php echo esc_attr( $item_id ); ?>" name="menu-item-poetheme-title[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_title ); ?> />
            <span><?php esc_html_e( 'Usa questo elemento come titolo/colonna (applica grassetto)', 'poetheme' ); ?></span>
        </label>
    </div>
    <div class="poetheme-menu-field poetheme-menu-field--icon">
        <label for="edit-menu-item-poetheme-icon-<?php echo esc_attr( $item_id ); ?>">
            <?php esc_html_e( 'Icona Lucide', 'poetheme' ); ?>
        </label>
        <div class="poetheme-icon-picker">
            <input type="text" class="widefat poetheme-icon-picker__input" id="edit-menu-item-poetheme-icon-<?php echo esc_attr( $item_id ); ?>" name="menu-item-poetheme-icon[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $icon_value ); ?>" placeholder="<?php esc_attr_e( 'es. star', 'poetheme' ); ?>" />
            <div class="poetheme-icon-picker__preview" data-target="edit-menu-item-poetheme-icon-<?php echo esc_attr( $item_id ); ?>">
                <?php if ( $icon_value ) : ?>
                    <i data-lucide="<?php echo esc_attr( $icon_value ); ?>" class="w-5 h-5"></i>
                <?php endif; ?>
            </div>
            <details class="poetheme-icon-picker__panel">
                <summary><?php esc_html_e( 'Mostra libreria icone', 'poetheme' ); ?></summary>
                <div class="poetheme-icon-picker__grid">
                    <?php foreach ( $icons as $icon ) : ?>
                        <button type="button" class="poetheme-icon-picker__option" data-icon="<?php echo esc_attr( $icon ); ?>" data-target="edit-menu-item-poetheme-icon-<?php echo esc_attr( $item_id ); ?>">
                            <span class="poetheme-icon-picker__option-icon"><i data-lucide="<?php echo esc_attr( $icon ); ?>" class="w-5 h-5"></i></span>
                            <span class="poetheme-icon-picker__option-label"><?php echo esc_html( $icon ); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </details>
        </div>
    </div>
    <?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'poetheme_nav_menu_item_custom_fields', 10, 4 );

/**
 * Save the custom mega menu fields.
 *
 * @param int $menu_id         Menu ID.
 * @param int $menu_item_db_id Menu item ID.
 */
function poetheme_save_menu_item_custom_fields( $menu_id, $menu_item_db_id ) {
    $is_title = isset( $_POST['menu-item-poetheme-title'][ $menu_item_db_id ] ); // phpcs:ignore WordPress.Security.NonceVerification

    if ( $is_title ) {
        update_post_meta( $menu_item_db_id, '_poetheme_menu_is_title', 1 );
    } else {
        delete_post_meta( $menu_item_db_id, '_poetheme_menu_is_title' );
    }

    if ( isset( $_POST['menu-item-poetheme-icon'][ $menu_item_db_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $icon_value = sanitize_text_field( wp_unslash( $_POST['menu-item-poetheme-icon'][ $menu_item_db_id ] ) );
        if ( $icon_value ) {
            update_post_meta( $menu_item_db_id, '_poetheme_menu_icon', $icon_value );
        } else {
            delete_post_meta( $menu_item_db_id, '_poetheme_menu_icon' );
        }
    }
}
add_action( 'wp_update_nav_menu_item', 'poetheme_save_menu_item_custom_fields', 10, 2 );

/**
 * Enqueue admin assets for the menu editor.
 *
 * @param string $hook Current admin page hook.
 */
function poetheme_enqueue_menu_admin_assets( $hook ) {
    if ( 'nav-menus.php' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'poetheme-menu-icon-picker', POETHEME_URI . '/assets/css/menu-icon-picker.css', array(), POETHEME_VERSION );
    wp_enqueue_script( 'poetheme-menu-lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js', array(), POETHEME_VERSION, true );
    wp_enqueue_script( 'poetheme-menu-icon-picker', POETHEME_URI . '/assets/js/menu-icon-picker.js', array( 'poetheme-menu-lucide' ), POETHEME_VERSION, true );
    wp_add_inline_script( 'poetheme-menu-icon-picker', 'window.addEventListener("load",function(){if(window.lucide){window.lucide.createIcons();}});' );
}
add_action( 'admin_enqueue_scripts', 'poetheme_enqueue_menu_admin_assets' );
