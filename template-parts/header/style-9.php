<?php
/**
 * Header layout: Style 9 (App Sidebar).
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$sidebar_id           = 'poetheme-app-sidebar';
$mobile_drawer_id     = 'poetheme-app-mobile-drawer';
$subheader_options    = poetheme_get_subheader_options();
$header_options       = function_exists( 'poetheme_get_header_options' ) ? poetheme_get_header_options() : array();
$show_title           = poetheme_subheader_should_display_title();
$show_breadcrumbs     = poetheme_subheader_should_display_breadcrumbs();
$breadcrumbs_items    = $show_breadcrumbs ? poetheme_get_breadcrumbs_items() : array();
$title_text           = '';
$title_tag            = isset( $subheader_options['title_tag'] ) ? strtolower( (string) $subheader_options['title_tag'] ) : 'h1';
$allowed_title_tags   = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
$title_classes        = array_merge( array( 'poetheme-app-page-title' ), poetheme_get_subheader_title_classes() );
$title_classes        = implode( ' ', array_map( 'sanitize_html_class', array_unique( $title_classes ) ) );
$show_app_header_intro  = ! empty( $header_options['show_app_header_intro'] );
$app_intro_title       = isset( $header_options['app_header_intro_title'] ) ? (string) $header_options['app_header_intro_title'] : '';
$app_intro_description = isset( $header_options['app_header_intro_description'] ) ? (string) $header_options['app_header_intro_description'] : '';

if ( '' === trim( $app_intro_title ) ) {
    $app_intro_title = __( 'Impostazioni testata', 'poetheme' );
}

if ( '' === trim( $app_intro_description ) ) {
    $app_intro_description = __( 'Configura layout, top bar, call to action e profili social.', 'poetheme' );
}

if ( ! in_array( $title_tag, $allowed_title_tags, true ) ) {
    $title_tag = 'h1';
}

if ( $show_title ) {
    $title_text = poetheme_get_subheader_title_text();
    if ( '' === trim( $title_text ) ) {
        $show_title = false;
    }
}
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
                <svg class="poetheme-app-sidebar__toggle-icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                    <rect x="4" y="5" width="16" height="14" rx="2" ry="2"></rect>
                    <path d="M10 5v14"></path>
                </svg>
                <span class="screen-reader-text" data-poetheme-app-sidebar-toggle-label>
                    <?php esc_html_e( 'Comprimi menu laterale', 'poetheme' ); ?>
                </span>
            </button>
        </div>

        <nav class="poetheme-app-sidebar__nav" aria-label="<?php esc_attr_e( 'Navigazione principale', 'poetheme' ); ?>">
            <?php
            poetheme_render_navigation_menu(
                'primary',
                'sidebar',
                array(
                    'menu_class' => 'poetheme-app-sidebar__menu',
                )
            );
            ?>
        </nav>

        <?php poetheme_render_site_author_profile( array( 'context' => 'desktop' ) ); ?>
    </aside>

    <div class="poetheme-app-main">
        <div class="poetheme-app-mobile-bar">
            <div class="poetheme-app-mobile-bar__brand">
                <?php poetheme_the_logo(); ?>
            </div>
            <button
                type="button"
                class="poetheme-app-mobile-toggle"
                data-poetheme-app-mobile-toggle
                aria-controls="<?php echo esc_attr( $mobile_drawer_id ); ?>"
                aria-expanded="false"
            >
                <span aria-hidden="true">☰</span>
                <span class="screen-reader-text"><?php esc_html_e( 'Apri menu mobile', 'poetheme' ); ?></span>
            </button>
        </div>

        <?php if ( $show_app_header_intro ) : ?>
            <section class="poetheme-app-intro-strip" aria-label="<?php esc_attr_e( 'Introduzione impostazioni testata', 'poetheme' ); ?>">
                <p class="poetheme-app-intro-strip__title"><?php echo esc_html( $app_intro_title ); ?></p>
                <?php if ( '' !== trim( $app_intro_description ) ) : ?>
                    <p class="poetheme-app-intro-strip__description"><?php echo esc_html( $app_intro_description ); ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ( $show_title || ! empty( $breadcrumbs_items ) ) : ?>
            <header class="poetheme-app-main-header">
                <div class="poetheme-app-main-header__title-wrap">
                <?php if ( $show_title ) : ?>
                    <<?php echo tag_escape( $title_tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>" itemprop="headline"><?php echo esc_html( $title_text ); ?></<?php echo tag_escape( $title_tag ); ?>>
                <?php endif; ?>
                </div>

                <?php if ( ! empty( $breadcrumbs_items ) ) : ?>
                    <nav class="poetheme-app-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'poetheme' ); ?>">
                        <?php echo poetheme_get_breadcrumbs_markup( $breadcrumbs_items, poetheme_get_breadcrumbs_separator() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </nav>
                <?php endif; ?>
            </header>
        <?php endif; ?>


<div class="poetheme-app-mobile-overlay" data-poetheme-app-mobile-overlay hidden></div>
<aside id="<?php echo esc_attr( $mobile_drawer_id ); ?>" class="poetheme-app-mobile-drawer" data-poetheme-app-mobile-drawer aria-label="<?php esc_attr_e( 'Menu mobile', 'poetheme' ); ?>" aria-hidden="true" hidden>
    <div class="poetheme-app-mobile-drawer__header">
        <div class="poetheme-app-mobile-drawer__brand">
            <?php poetheme_the_logo(); ?>
        </div>
        <button type="button" class="poetheme-app-mobile-drawer__close" data-poetheme-app-mobile-close aria-controls="<?php echo esc_attr( $mobile_drawer_id ); ?>">
            <span aria-hidden="true">×</span>
            <span class="screen-reader-text"><?php esc_html_e( 'Chiudi menu mobile', 'poetheme' ); ?></span>
        </button>
    </div>
    <nav class="poetheme-app-mobile-drawer__nav" aria-label="<?php esc_attr_e( 'Navigazione principale mobile', 'poetheme' ); ?>">
        <?php
        poetheme_render_navigation_menu(
            'primary',
            'mobile',
            array(
                'menu_class' => 'poetheme-app-mobile-drawer__menu',
            )
        );
        ?>
    </nav>
    <?php poetheme_render_site_author_profile( array( 'context' => 'mobile' ) ); ?>
</aside>
