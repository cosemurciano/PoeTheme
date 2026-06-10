<?php
/**
 * Header layout: Style 9 (App Sidebar).
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$sidebar_id        = 'poetheme-app-sidebar';
$title_text        = poetheme_get_subheader_title_text();
$breadcrumbs_items = poetheme_get_breadcrumbs_items();
?>
<div class="poetheme-app-shell poetheme-app-shell--sidebar-expanded" data-poetheme-app-shell>
    <aside id="<?php echo esc_attr( $sidebar_id ); ?>" class="poetheme-app-sidebar" aria-label="<?php esc_attr_e( 'Menu laterale del sito', 'poetheme' ); ?>">
        <div class="poetheme-app-sidebar__header">
            <div class="poetheme-app-sidebar__brand">
                <?php poetheme_the_logo(); ?>
            </div>
            <button
                type="button"
                class="poetheme-app-sidebar__toggle"
                data-poetheme-app-sidebar-toggle
                aria-expanded="true"
                aria-controls="<?php echo esc_attr( $sidebar_id ); ?>"
            >
                <span class="poetheme-app-sidebar__toggle-icon" aria-hidden="true"></span>
                <span class="screen-reader-text" data-poetheme-app-sidebar-toggle-label>
                    <?php esc_html_e( 'Comprimi menu laterale', 'poetheme' ); ?>
                </span>
            </button>
        </div>

        <nav class="poetheme-app-sidebar__nav" aria-label="<?php esc_attr_e( 'Navigazione principale', 'poetheme' ); ?>">
            <?php
            poetheme_render_navigation_menu(
                'primary',
                'desktop',
                array(
                    'menu_class' => 'poetheme-app-sidebar__menu',
                )
            );
            ?>
        </nav>

        <?php poetheme_render_site_author_profile(); ?>
    </aside>

    <div class="poetheme-app-main">
        <header class="poetheme-app-main-header">
            <div class="poetheme-app-main-header__title-wrap">
                <?php if ( '' !== trim( $title_text ) ) : ?>
                    <h1 class="poetheme-app-page-title" itemprop="headline"><?php echo esc_html( $title_text ); ?></h1>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $breadcrumbs_items ) ) : ?>
                <nav class="poetheme-app-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'poetheme' ); ?>">
                    <?php echo poetheme_get_breadcrumbs_markup( $breadcrumbs_items, poetheme_get_breadcrumbs_separator() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </nav>
            <?php endif; ?>
        </header>
